<?php
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use yii\console\Application;
use yii\di\Container;

/**
 * Базовый класс для всех тестов
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
abstract class AbstractTestCase extends TestCase
{
    /** @var string файл тестоа */
    protected $filename = __DIR__ . '/test.dat';

    protected function deleteFiles()
    {
        @unlink($this->filename);
    }

    /**
     * Create Yii application
     *
     * @return \yii\console\Application
     */
    public function setUp()
    {
        $this->deleteFiles();

        return new Application([
        	'id' => 'testapp',
        	'basePath' => __DIR__,
        	'vendorPath' => dirname(__DIR__).'/vendor',
        	'components' => [
        		'db' => [
        			'class' => 'yii\db\Connection',
        			'dsn' => 'sqlite::memory:',
        		],
        	],
        ]);
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    public function tearDown()
    {
        \Yii::$app = null;
        \Yii::$container = new Container();

        $this->deleteFiles();
    }

    /**
     * Тест модели
     */
    public function testModel()
    {
        // создаем новую модель
        $testModel = TestModel::instance(true);
        self::assertSame(null, $testModel->float);

        // проверка singleton экремпляра
        self::assertEquals($testModel, TestModel::instance(false));

        // загружаем в модель данные
        $testModel->setAttributes(TestModel::DATA, false);
        self::assertSame(TestModel::DATA, $testModel->attributes);

        // сохраняем модель
        self::assertEquals(true, $testModel->save());

        // пересоздаем модель
        $testModel2 = TestModel::instance(true);
        self::assertNotEquals($testModel, $testModel2);

        // проверяем загруженные данные
        self::assertSame(TestModel::DATA, $testModel2->attributes);
    }
}