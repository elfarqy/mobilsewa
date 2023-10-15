<?php

/** @var yii\web\View $this */

$this->title = 'Perijinan Sewa';

\backend\assets\PerijinanAsset::register($this);
?>
<div class="site-index">
    <?= \yii\bootstrap5\Html::a('Export', \yii\helpers\Url::to(['site/export']),['class' => 'btn btn-success btn-sm'])?>
    <div class="table-responsive">
        <?php
        \mimicreative\datatables\widgets\DataTable::begin([
            'asset'        => \mimicreative\datatables\assets\DataTableAsset::class,
            'tableOptions' => [
                'id' => 'approval-list',
            ],
            'dataTable'    => [
                'serverSide' => true,
                'autoWidth'  => false,
                'processing' => true,
                'dom' => 'ftp',
                'pageLength' => 50,
                'ajax'        => [
                    'type' => 'POST',
                    'data' => new \yii\web\JsExpression("
                        function (data) {
                            data[yii.getCsrfParam()] = yii.getCsrfToken();
                            return data;
                        }
                    ")
                ],
                'columns'     => [
                    [
                        'title' => 'Driver',
                        'data'  => 'driver_id',
                    ],
                    [
                        'title' => 'Kendaraan',
                        'data'  => 'vehicle_id',
                    ],
                    [
                        'title' => 'Status',
                        'data'  => 'status',
                    ],
                    [
                        'title'     => 'Dibuat Tanggal',
                        'data'      => 'created_at',
                    ],
//                    [
//                        'class' => \mimicreative\datatables\columns\ActionsColumn::class,
//                        'title' => 'Actions',
//                    ],
                ]
            ]
        ]);

        \mimicreative\datatables\widgets\DataTable::end();

        ?>
    </div>


</div>
