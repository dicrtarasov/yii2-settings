<?php
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
    public function setUp()
    {
        parent::setUp()->set('settings', new YamlSettingsStore([
            'filename' => $this->filename
        ]));
    }
}
