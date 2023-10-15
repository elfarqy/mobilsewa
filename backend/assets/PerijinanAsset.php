<?php

namespace backend\assets;

use yii\web\AssetBundle;

class PerijinanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/ijin.js'
    ];

    public $depends = [
        AppAsset::class,
        Select2Asset::class
    ];

}