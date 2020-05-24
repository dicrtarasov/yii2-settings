<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 24.05.20 14:04:10
 */

/** @noinspection PhpUnused */
declare(strict_types = 1);

error_reporting(- 1);
ini_set('display_errors', '1');

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@dicr/tests', __DIR__);
Yii::setAlias('@dicr/settings', dirname(__DIR__) . '/src');

