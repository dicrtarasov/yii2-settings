<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:37:41
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\settings\SerializeSettingsStore;
use Yii;
use yii\base\Exception;

/**
 * Test SerializeSettingsStore
 */
class SerializeSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();

        Yii::$app->set('settings', new SerializeSettingsStore([
            'filename' => self::$filename
        ]));
    }
}
