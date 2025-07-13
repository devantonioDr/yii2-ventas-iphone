<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
          'targets' => [
            [
              'class' => 'yii\log\FileTarget',
              // 'levels' => ['warning','error'],
              'categories' => ['yii\web\*'],
              'except' => ['yii\web\aplication::handleRequest/logs'],
              'logVars' => ['_GET', '_POST'],
              'logFile' => '@runtime/logs/test.log',
            ],
            [
              'class' => 'yii\log\DbTarget',
              'logVars' => ['_GET', '_POST'],
              'except' => ['@web/js/*'],  
              'categories' => ['yii\web\Application::handleRequest'],
              'prefix' => function ($message) {
                $uid = !empty(YII::$app->user->id) ? YII::$app->user->id : 1;
                return "{$uid}";
              },
            ],
          ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
