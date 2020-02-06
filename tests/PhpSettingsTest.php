<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:03:28
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\PhpSettingsStoreStore;

/**
 * Test PhpSettingsStore
 */
class PhpSettingsTest extends AbstractTestCase
{
    /**
     * @inheritDoc
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function setUp()
    {
        parent::setUp()->set('settings', new PhpSettingsStoreStore([
            'filename' => $this->filename
        ]));
    }
}
