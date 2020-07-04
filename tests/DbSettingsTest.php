<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 21:00:28
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\DbSettingsStore;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * Test PhpSettingsStore
 */
class DbSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     *
     * @throws InvalidConfigException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Yii::$app->setComponents([
            'db' => [
                'class' => Connection::class,
                'dsn' => 'sqlite::memory:'
            ],
            'settings' => [
                'class' => DbSettingsStore::class
            ]
        ]);
    }
}
