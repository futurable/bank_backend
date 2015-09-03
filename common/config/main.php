<?php
$db_core = require(__DIR__ . '/db_core.php');
$db_bank = require(__DIR__ . '/db_bank.php');

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'components' => [
        'db_core' => $db_core,
        'db' => $db_bank,
        'db_bank' => $db_bank,
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
