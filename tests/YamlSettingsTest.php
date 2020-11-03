<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:59:11
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\settings\YamlSettingsStore;
use Yii;
use yii\base\Exception;

/**
 * Test PhpSettingsStore
 */
class YamlSettingsTest extends AbstractTestCase
{
    /**
     * @throws Exception
     */
    public static function setUpBeforeClass() : void
    {
        parent::setUpBeforeClass();

        Yii::$app->set('settings', new YamlSettingsStore([
            'filename' => self::$filename
        ]));
    }
}
