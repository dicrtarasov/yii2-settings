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
    public function setUp()
    {
        parent::setUp()->set('settings', new YamlSettingsStore([
            'filename' => $this->filename
        ]));
    }
}
