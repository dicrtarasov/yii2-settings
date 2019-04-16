<?php
namespace dicr\tests;

use dicr\settings\PhpSettingsStore;

/**
 * Test PhpSettingsStore
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class PhpSettingsTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp()->set('settings', new PhpSettingsStore([
            'filename' => $this->filename
        ]));
    }
}
