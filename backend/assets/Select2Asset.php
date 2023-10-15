<?php

namespace backend\assets;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public $sourcePath = '@npm/select2';
    public $css = [
        'dist/css/select2.min.css',
    ];
    public $js = [
        'dist/js/select2.full.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];

}