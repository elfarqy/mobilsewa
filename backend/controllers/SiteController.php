<?php

namespace backend\controllers;

use backend\models\PermissionTransportForm;
use backend\utils\ErrorFormatter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use common\models\Approval;
use common\models\LoginForm;
use common\models\User;
use common\models\VehicleUsage;
use mimicreative\datatables\actions\DataTableAction;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'manager', 'approval', 'export', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new PermissionTransportForm();
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        if ($request->isPost){
            $model->load($request->post());
            $key = 'error';
            $message = '';

            if ($model->save()){
                $key = 'success';
                $message = 'Successfully created';
            } else {
                $message = implode(',',ErrorFormatter::flat($model->errors));
            }

            $session->setFlash($key, $message);

        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionApproval()
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user->identity;

        if ($request->isAjax) {

            $query = VehicleUsage::find()
                ->where([
                    '<>',VehicleUsage::tableName() . '.status', 'deleted'
                ]);
            $approvalIndex = Approval::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['approval_status' => 'pending'])
                ->all();

            $indexedByApprovals =ArrayHelper::index($approvalIndex, 'vehicle_usage_id');


            return (new DataTableAction('company-datatable-list', $this, [
                'requestMethod' => DataTableAction::REQUEST_METHOD_POST,
                'query' => $query,
                'toArrayProperties' => [
                    VehicleUsage::class => [
                        'driver_id' => function($model) {
                            return $model->driver->username;
                        },
                        'vehicle_id' => function($model){
                            return $model->vehicle->name;
                        },
                        'created_at',
                        'status',
//                        'actions' => function ($model) use ($indexedByApprovals, $user) {
//                            $responses = [];
//
//                            if (!array_key_exists($model->id, $indexedByApprovals)){
//                                return $responses;
//                            }
//                            $selectedApproval = $indexedByApprovals[$model->id];
//
//                            if ($selectedApproval->user_id == $user->id){
//                                $responses[] = [
//                                    'label' => ' Verify',
//                                    'url' => Url::to(['/company/verify', 'id' => $model->id]),
//                                    'options' => [
//                                        'class' => 'btn btn-success btn-sm',
//                                    ]
//                                ];
//                            }
//
//                            return $responses;
//                        }
                    ]
                ]
            ]))->run();
        }
        $this->view->title = 'Approval form';
        $this->view->params['breadcrumbs'] = [
            [
                'label' => 'Approval',
            ]
        ];
        return $this->render('approval');
    }

    public function actionExport()
    {
        $query = (new Query())->select('count(*) as max, vehicle_usage_id')->from(Approval::tableName())
            ->groupBy(['vehicle_usage_id'])->all();
        usort($query, function ($a, $b){
            return strnatcmp($a['max'], $b['max']);
        });
        $lastRecord = end($query);
        $header = [
            'Driver',
            'Kendaraan',
            'Dibuat Tanggal',
            'Status'
        ];

        $counter = 3;
        $maxApprovals = (int) $lastRecord['max'] - 1;
        for($i = 0; $i <= $maxApprovals; $i++){
            $counter += 1;
            $tmpC = $i;
            $tmp = $tmpC +=1;
            $header[$counter] = "Approval ke {$tmp}";
            $counter += 1;
            $header[$counter] = "Status Approval ke {$tmp}";
        }

        $writer = WriterEntityFactory::createXLSXWriter();
        $currentDate = date('Y-m-d-m-Y-H-i-s');
        $writer->openToBrowser("{$currentDate}.xlsx");
        $rowFromValues = WriterEntityFactory::createRowFromArray($header);
        $writer->addRow($rowFromValues);

        $query = VehicleUsage::find()
            ->where([
                '<>',VehicleUsage::tableName() . '.status', 'deleted'
            ])->all();


        $approvalIndex = Approval::find()
            ->all();

        $indexedByApprovals =ArrayHelper::index($approvalIndex, null,'vehicle_usage_id');
        $listOfUser = User::find()->where(['in', 'id', ArrayHelper::getColumn($approvalIndex, 'user_id')])->all();

        $indexedUser = ArrayHelper::index($listOfUser, 'id');

        foreach ($query as $value){
            $row = [
                $value->driver->username,
                $value->vehicle->name,
                $value->created_at,
                $value->status
            ];

            $counterApp = $maxApprovals + 1;

            if(array_key_exists($value->id, $indexedByApprovals)){
                $approver = $indexedByApprovals[$value->id];
                usort($approver, function ($a, $b){
                    return strnatcmp($a['sequence'], $b['sequence']);
                });
                $counterRow = 3;
                foreach ($approver as $approvi){
                    $counterRow += 1;
                    $row[$counterRow] = $indexedUser[$approvi['user_id']]['username'];
                    $counterRow += 1;
                    $row[$counterRow] = $approvi['status'];

                    $counterApp -= 1;
                }

            }

            if ($counterApp > 0){
                $tmpCounter = 0;
                while (true):
                    $counterRow += 1;
                    $row[$counterRow] = '--';
                    $counterRow += 1;
                    $row[$counterRow] = '--';

                    $tmpCounter += 1;

                    if ($tmpCounter >= $counterApp){
                        break;
                    }
                    endwhile;
            }

            $rowFromValues = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($rowFromValues);

        }

        $writer->close();
    }

    public function actionDashboard()
    {
        $query = (new Query())->select('count(*) as total_count, vehicle.name')
            ->from(VehicleUsage::tableName())
            ->leftJoin('vehicle', 'vehicle.id = vehicle_usage.vehicle_id')
            ->groupBy('vehicle.name')->all();

        $request = Yii::$app->request;
        $response = Yii::$app->response;

        if ($request->isAjax){
            $response->format = Response::FORMAT_JSON;

            foreach ($query as $value){
                $targetCategory[] = [
                    'y'            => $value['name'],
                    "barThickness"    => 1,
                    'maxBarThickness' => 20,
                    'x'               => $value['total_count'],
                    'borderColor'     => '#' . dechex(rand(0, 10000000)),
                    'backgroundColor' => '#' . dechex(rand(0, 10000000)),
                ];
            }



            return [
                'data' => [
                    'targetCategory' => $targetCategory
                ]
            ];


        }
        return $this->render('dashboard',compact('query'));
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionManager($filter = null)
    {
        $req = \Yii::$app->request;
        $resp = \Yii::$app->response;
        $resp->format = Response::FORMAT_JSON;
        $q = $req->get('q');

        if (!$req->isAjax) {
            throw new  NotFoundHttpException();
        }

        $customer = User::find()->where(['role' => 'manager'])
            ->andFilterWhere([
                'OR',
                ['like', 'username', $q]
            ]);

        $counter = $customer->count('id');

        $limit = 20;
        $page = $req->get('page', 1);
        $offset = ($page - 1) * $limit;
        $result = $customer->limit($limit)->offset($offset);

        return [
            'pagination' => [
                'more' => (($offset + $limit) < $counter)
            ],
            'results' => ArrayHelper::toArray($result->all(), [
                User::class => [
                    'id',
                    'text' => function (User $model) {
                        return $model->username;
                    }
                ]
            ])
        ];
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
