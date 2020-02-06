<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:02:53
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\DbSettingsStore;

/**
 * Test PhpSettingsStore
 */
class DbSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function setUp()
    {
        parent::setUp()->set('settings', new DbSettingsStore());
    }
}
