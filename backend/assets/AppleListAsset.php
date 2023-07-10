<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AppleListAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'js/list.js'
    ];
    public $depends = [
        VueAsset::class,
    ];
}