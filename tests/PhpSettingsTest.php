<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:58:55
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\PhpSettingsStore;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Test PhpSettingsStore
 */
class PhpSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();

        Yii::$app->set('settings', new PhpSettingsStore([
            'filename' => self::$filename
        ]));
    }
}
