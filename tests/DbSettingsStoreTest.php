<?php
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use dicr\settings\DbSettingsStore;

/**
 * Test PhpSettingsStore
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class DbSettingsStoreTest extends TestCase
{

    public function testModel()
    {
        $store = new DbSettingsStore();
        $model = new TestModel(TestModel::DATA);
        $store->save($model);

        $store = new DbSettingsStore();
        $model = new TestModel();
        $store->load($model);

        self::assertEquals(TestModel::DATA, $model->attributes);
    }
}
