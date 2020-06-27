<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.06.20 20:44:01
 */

declare(strict_types = 1);
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use Yii;
use yii\console\Application;
use yii\db\Connection;
use yii\di\Container;
use function dirname;

/**
 * Базовый класс для всех тестов
 */
abstract class AbstractTestCase extends TestCase
{
    /** @var string файл тестоа */
    protected $filename = __DIR__ . '/test.dat';

    protected function deleteFiles()
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @unlink($this->filename);
    }

    /**
     * @inheritDoc
     * @throws \yii\base\InvalidConfigException
     */
    protected function setUp()
    {
        $this->deleteFiles();

        return new Application([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => Connection::class,
                    'dsn' => 'sqlite::memory:',
                ],
            ],
        ]);
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     *
     * @noinspection DisallowWritingIntoStaticPropertiesInspection
     */
    protected function tearDown()
    {
        Yii::$app = null;
        Yii::$container = new Container();

        $this->deleteFiles();
    }

    /**
     * Тест модели
     *
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
     */
    public function testModel()
    {
        // создаем новую модель
        $testModel = TestModel::instance(true);
        self::assertNull($testModel->float);

        // проверка singleton экземпляра
        self::assertEquals($testModel, TestModel::instance());

        // загружаем в модель данные
        $testModel->setAttributes(TestModel::TEST_DATA);
        self::assertSame(TestModel::TEST_DATA, $testModel->attributes);

        // сохраняем модель
        self::assertTrue($testModel->save());

        // пересоздаем модель
        $testModel2 = TestModel::instance(true);
        self::assertNotEquals($testModel, $testModel2);

        // проверяем загруженные данные
        self::assertSame(TestModel::TEST_DATA, $testModel2->attributes);
    }
}
