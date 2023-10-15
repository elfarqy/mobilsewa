<?php

namespace backend\controllers;

use backend\models\LoginModel;
use backend\models\UserTransactionModel;
use backend\utils\ErrorFormatter;
use backend\utils\HttpException;
use backend\utils\ReservedCurrency;
use backend\utils\Response;
use Codeception\Util\HttpCode;
use common\models\Approval;
use common\models\Company;
use common\models\LoginForm;
use common\models\User;
use common\models\VehicleUsage;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];

        $behaviors['authenticator'] = [
            'class'       => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class
            ],
            'except' => [
                'login'
            ]
        ];


        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'login' => ['post'],
            'approvals' => ['get'],
        ];
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        $request->enableCookieValidation = false;
        $request->enableCsrfCookie = false;
        $request->parsers = [
            'application/json' => \yii\web\JsonParser::class,
        ];

        try {
            $loginForm        = new LoginForm();
        } catch (InvalidConfigException $e) {
            throw new \yii\web\HttpException(400, [$e->getMessage()]);
        }

        // load the submitted data
        $loginForm->load(\Yii::$app->request->post(), '');

        if ($loginForm->login()) {
            $user = \Yii::$app->user->identity;

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = 'Login to application success';
            $response->code    = '001';
            $response->status  = 200;
            $response->data    = [
                'user' => ArrayHelper::toArray($user, [
                    User::class        => [
                        'username',
                        'email',
                        'token' => function ($model) {
                            return $model->auth_key;
                        }
                    ],
                ])
            ];

            return $response;
        }

        $errMessage = implode(",", ErrorFormatter::flat($loginForm->errors));

        throw new \yii\web\HttpException(400, "Failed to Login: {$errMessage}");

    }

    public function actionApprovals()
    {
        $request = Yii::$app->request;
        $request->enableCookieValidation = false;
        $request->enableCsrfCookie = false;
        $request->parsers = [
            'application/json' => \yii\web\JsonParser::class,
        ];

        $query = VehicleUsage::find()
            ->where([
                '<>',VehicleUsage::tableName() . '.status', 'deleted'
            ]);

        $user = Yii::$app->user->identity;

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => 10]);

        $vehicleUsage = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $approvalIndex = Approval::find()
            ->where(['in','vehicle_usage_id', ArrayHelper::getColumn($vehicleUsage, 'id')])
            ->andWhere(['approval_status' => 'pending'])
            ->all();

        $indexedByApprovals =ArrayHelper::index($approvalIndex, null,'vehicle_usage_id');

        $pageCount = $pagination->pageCount;
        $currentPage = $pagination->page + 1; // zer0 based

        $data = [
            'total_page'  => $pageCount,
            'lastPage'    => $pageCount,
            'nextPage'    => $currentPage < $pageCount ? $currentPage + 1 : $pageCount,
            'currentPage' => $currentPage,
            'prevPage'    => $currentPage >= 2 ? $currentPage - 1 : $currentPage,
            "companies" => ArrayHelper::toArray($vehicleUsage, [
                VehicleUsage::class =>[
                    'id',
                    'driver' => function($model){
                        return $model->driver->username;
                    },
                    'vehicle' => function($model){
                        return $model->vehicle->name;
                    } ,
                    'created_at',
                    'status',
                    'available' => function($model) use($indexedByApprovals, $user){
                        if (!array_key_exists($model->id, $indexedByApprovals)){
                            return false;
                        }

                        $selectedApprovals = $indexedByApprovals[$model->id];

                        usort($selectedApprovals, function ($a, $b){
                            return strnatcmp($a['sequence'], $b['sequence']);
                        });

                        $firstApproval = reset($selectedApprovals);

                        return $firstApproval['user_id'] == $user->id;

                    }
                ]
            ]),
        ];

        $response          = new Response();
        $response->name    = 'Success';
        $response->message = 'Approval list';
        $response->code    = '001';
        $response->status  = 200;
        $response->data    = $data;

        return $response;

    }

    public function actionApprove()
    {
        $request = Yii::$app->request;
        $approveId = $request->get('approve', null);

        if (empty($approveId)){
            throw new \yii\web\HttpException(400, 'Invalid param');
        }

        $approve = Approval::find()
            ->where(['id' => $approveId])
            ->andWhere(['approval_status' => 'pending'])
            ->andWhere(['user_id' => Yii::$app->user->identity->id])
            ->one();
        if (empty($approve)){
            throw new NotFoundHttpException();
        }

        $OldApproval = Approval::find()
            ->where(['vehicle_usage_id' => $approve->vehicle_usage_id])
            ->andWhere(['<', 'sequence', $approve->sequence])
            ->andWhere(['approval_status' => 'pending'])
            ->andWhere(['<>', 'user_id', $approve->user_id])
            ->count();

        if ($OldApproval > 0){
            throw new \yii\web\HttpException(400, 'Previous approval not yet approved');
        }

        $transaction = Yii::$app->db->beginTransaction();

        $approve->approval_status = 'approved';

        if (!$approve->save()){
            throw new \yii\web\HttpException(400,implode(',',ErrorFormatter::flat($approve->errors)));
        }

        try {
            $transaction->commit();
        } catch (Exception $exception){
            $transaction->rollBack();
        }

        $response          = new Response();
        $response->name    = 'Success';
        $response->message = 'Approval updated';
        $response->code    = '002';
        $response->status  = 200;
        $response->data    = [];

        return $response;

    }

}