<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'America/Santo_Domingo',
    'components' => [
        'requestLogger' => [
            'class' => 'common\components\RequestLogger',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
        ]
    ],
    'modules' => [
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
        ],
        'logreader' => [
            'class' => 'zhuravljov\yii\logreader\Module',
            'aliases' => [
                'Frontend Errors' => '@frontend/runtime/logs/app.log',
                'Backend Errors' => '@backend/runtime/logs/app.log',
                'Console Errors' => '@console/runtime/logs/app.log',
            ],
        ],
    ],

    'on beforeRequest' => function ($event) {
        Yii::$app->requestLogger->setStartTime();
    },
    'on afterRequest' => function ($event) {
        Yii::$app->requestLogger->logRequest();
    },

];
