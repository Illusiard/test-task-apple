<?php

use common\models\User;
use yii\log\FileTarget;
use yii\web\UrlManager;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id'                  => 'app-backend',
    'basePath'            => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap'           => ['log'],
    'modules'             => [],
    'components'          => [
        'request'      => [
            'csrfParam' => '_csrf-backend',
        ],
        'user'         => [
            'identityClass'   => User::class,
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session'      => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'   => [
            'class'           => UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                //'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params'              => $params,
];
