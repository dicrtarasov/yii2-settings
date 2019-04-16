<?php
namespace dicr\tests;

use dicr\settings\DbSettingsStore;

/**
 * Test PhpSettingsStore
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DbSettingsTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp()->set('settings', new DbSettingsStore());
    }
}
