<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 21:00:28
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\SettingsException;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Application;
use yii\db\Connection;
use yii\di\Container;
use function dirname;
use function unlink;

/**
 * Базовый класс для всех тестов
 */
abstract class AbstractTestCase extends TestCase
{
    /** @var string файл для хранения данных тестоа */
    protected const FILENAME = __DIR__ . '/test.dat';

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public static function setUpBeforeClass(): void
    {
        new Application([
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
    public static function tearDownAfterClass(): void
    {
        Yii::$app = null;
        Yii::$container = new Container();

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @unlink(self::FILENAME);
    }

    /**
     * Тест модели
     *
     * @throws SettingsException
     * @throws InvalidConfigException
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
