<?php \backend\assets\DashboardAsset::register($this);?>

<div class="site-index">

    <?php foreach ($query as $item):?>
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    Digunakan <?= $item['total_count'] ?>x
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $item['name'] ?></h5>
                </div>
            </div>
        </div>
    <?php endforeach;?>

    <canvas id="dashboardplot" class="plot" data-url="<?= \yii\helpers\Url::to(['site/dashboard'])?>"></canvas>


</div>