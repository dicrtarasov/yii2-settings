<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 24.05.20 14:03:30
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\settings\YamlSettingsStore;

/**
 * Test PhpSettingsStore
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class YamlSettingsTest extends AbstractTestCase
{
    /**
     * @return void|\yii\console\Application
     * @throws \yii\base\InvalidConfigException
     */
    protected function setUp()
    {
        parent::setUp()->set('settings', new YamlSettingsStore([
            'filename' => $this->filename
        ]));
    }
}
