<?php
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use dicr\settings\YamlSettingsStore;

/**
 * Test PhpSettingsStore
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class YamlSettingsStoreTest extends TestCase
{

    public function testModel()
    {
        $filename = __DIR__ . '/test.dat';

        $store = new YamlSettingsStore(['filename' => $filename]);

        $model = new TestModel(TestModel::DATA);
        $store->save($model);

        $store = new YamlSettingsStore(['filename' => $filename]);

        $model = new TestModel();
        $store->load($model);
        unlink($filename);

        self::assertEquals(TestModel::DATA, $model->attributes);
    }
}