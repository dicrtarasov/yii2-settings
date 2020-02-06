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

/**
 * Test SerializeSettingsStore
 */
class SerializeSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     * @return void|\yii\console\Application
     * @throws \yii\base\InvalidConfigException
     */
    public function setUp()
    {
        parent::setUp()->set('settings', new SerializeSettingsStore([
            'filename' => $this->filename
        ]));
    }
}
