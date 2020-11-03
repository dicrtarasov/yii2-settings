<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:37:41
 */

declare(strict_types = 1);

error_reporting(-1);
ini_set('display_errors', '1');

/**  */
define('YII_ENV', 'dev');

/**  */
define('YII_DEBUG', true);

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

/** @noinspection PhpUnhandledExceptionInspection */
new yii\console\Application([
    'id' => 'testapp',
    'basePath' => dirname(__DIR__),
    'components' => [
        'cache' => [
            'class' => yii\caching\FileCache::class
        ],
        'log' => [
            'targets' => [
                'file' => [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info', 'trace']
                ]
            ],
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => 'sqlite::memory:',
        ],
    ],
    'bootstrap' => ['log']
]);

