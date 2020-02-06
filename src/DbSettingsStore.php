<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:34:41
 */

declare(strict_types = 1);
namespace dicr\settings;

use dicr\helper\ArrayHelper;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Schema;
use yii\di\Instance;
use yii\helpers\Json;
use function in_array;
use function is_array;

/**
 * Настройки, хранимые в таблице базы данных.
 *
 * @noinspection MissingPropertyAnnotationsInspection
 */
class DbSettingsStore extends AbstractSettingsStore
{
    /** @var \yii\db\Connection база данных */
    public $db = 'db';

    /** @var string имя таблицы в базе данных */
    public $tableName = '{{settings}}';

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function init()
    {
        $this->db = Instance::ensure($this->db, Connection::class);

        $this->tableName = trim($this->tableName);
        if (empty($this->tableName)) {
            throw new InvalidConfigException('tableName');
        }

        $this->initDatabase();
    }

    /**
     * Инициализарует базу данных (создает таблицу).
     *
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    protected function initDatabase()
    {
        $schema = $this->db->getSchema();

        if (! in_array($schema->getRawTableName($this->tableName), $schema->tableNames, true)) {
            $this->db->createCommand()
                ->createTable($this->tableName, [
                    'module' => Schema::TYPE_STRING . ' NOT NULL',
                    'name' => Schema::TYPE_STRING . ' NOT NULL',
                    'value' => Schema::TYPE_TEXT
                ])
                ->execute();

            $this->db->createCommand()
                ->createIndex('module-name', $this->tableName, ['module', 'name'], true)
                ->execute();
        }
    }

    /**
     * Кодирует значение для сохранения в базу.
     *
     * @param mixed $value значение
     * @return string строковое значение
     */
    protected function encodeValue($value)
    {
        try {
            return Json::encode($value);
        } catch (Throwable $ex) {
            Yii::error($ex, __METHOD__);

            return null;
        }
    }

    /**
     * Декодирует значение из базы
     *
     * @param string|null $value
     * @return mixed
     */
    protected function decodeValue($value)
    {
        try {
            return Json::decode($value);
        } catch (Throwable $ex) {
            Yii::warning($ex, __METHOD__);

            return null;
        }
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::get()
     */
    public function get(string $module, string $name = null, $default = null)
    {
        $query = (new Query())->select('value')
            ->from($this->tableName)
            ->where(['module' => $module]);

        if ($name !== null) {
            // запрос одного значения
            $value = $query->andWhere(['name' => $name])
                ->limit(1)
                ->scalar($this->db);

            return $value ?? $this->decodeValue($value);
        }

        // запрос всех значение модели
        $query->addSelect('name')
            ->indexBy('name');

        $values = array_map(function(string $val) {
            return $this->decodeValue($val);
        }, $query->column($this->db));

        if (is_array($default)) {
            $values = ArrayHelper::merge($default, $values);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     * @see \dicr\settings\AbstractSettingsStore::set()
     */
    public function set(string $module, $name, $value = null)
    {
        foreach (is_array($name) ? $name : [$name => $value] as $key => $val) {
            if ($val === null || $val === '') {
                $this->delete($module, $key);
            } else {
                // для совместимости с sqlite делаем delete/insert вместо on-duplicate key
                $this->db->createCommand()->delete($this->tableName, [
                    'module' => $module,
                    'name' => $key
                ])->execute();

                /** @noinspection MissedFieldInspection */
                $this->db->createCommand()->insert($this->tableName, [
                    'module' => $module,
                    'name' => $key,
                    'value' => $this->encodeValue($val)
                ])->execute();
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     * @see \dicr\settings\AbstractSettingsStore::delete()
     */
    public function delete(string $module, string $name = null)
    {
        $conds = ['module' => $module];

        if ($name !== null) {
            $conds['name'] = $name;
        }

        $this->db->createCommand()
            ->delete($this->tableName, $conds)
            ->execute();

        return $this;
    }
}
