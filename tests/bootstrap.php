<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 14.05.21 23:13:37
 */

declare(strict_types = 1);

error_reporting(-1);
ini_set('display_errors', '1');

/**  */
const YII_ENV = 'dev';

/**  */
const YII_DEBUG = true;

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

