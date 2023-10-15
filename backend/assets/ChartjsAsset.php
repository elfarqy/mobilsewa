<?php

namespace backend\assets;

use yii\web\AssetBundle;

class ChartjsAsset extends AssetBundle
{
    public $sourcePath = '@npm/chart.js/dist';
//    public $baseUrl = '@web';
    public $js = [
        'chart.umd.js',
    ];
    public $depends = [
        AppAsset::class
    ];

}