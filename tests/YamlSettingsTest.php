<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 20:59:50
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\settings\YamlSettingsStore;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Application;

/**
 * Test PhpSettingsStore
 */
class YamlSettingsTest extends AbstractTestCase
{
    /**
     * @return void|Application
     * @throws InvalidConfigException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Yii::$app->setComponents([
            'settings' => [
                'class' => YamlSettingsStore::class,
                'filename' => self::FILENAME
            ]
        ]);
    }
}
