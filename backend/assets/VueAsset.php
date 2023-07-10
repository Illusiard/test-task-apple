<?php

namespace backend\assets;

use yii\web\AssetBundle;

class VueAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'js/vue.js'
    ];
    public $depends = [
        AppAsset::class,
    ];
}
