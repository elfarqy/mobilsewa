<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl  = '@web';
    public $js       = [
        'js/dashboard.js',
    ];
    public $depends  = [
        ChartjsAsset::class,

    ];

}