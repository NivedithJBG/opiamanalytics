<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            // web error handler
            'class' => '\bedezign\yii2\audit\components\web\ErrorHandler',
            // console error handler
            //'class' => '\bedezign\yii2\audit\components\console\ErrorHandler',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
        'migrate' => [
            'class'               => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                 # Other migration namespaces
                'bedezign\yii2\audit\migrations',
              ],
      ],
    ],
     
    
];

if (YII_ENV_DEV && class_exists('yii\gii\Module')) {
    // configuration adjustments for 'dev' environment.
    // Guarded on class_exists() because some deployments (e.g. GOSPL)
    // run YII_ENV=dev without the gii package in vendor/ — without this
    // guard, bootstrapping gii there fails and takes down every
    // console command (php yii ...), not just gii itself.
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
