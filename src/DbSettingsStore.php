<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 14.05.21 23:10:30
 */

declare(strict_types = 1);
namespace dicr\settings;

use Throwable;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Schema;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_key_exists;
use function in_array;
use function is_array;

/**
 * Настройки, хранимые в таблице базы данных.
 */
class DbSettingsStore extends Component implements SettingsStore
{
    /** @var string кодирование значения в строку, объекты сохраняются toString, восстанавливаются строки */
    public const FORMAT_STRING = 'string';

    /** @var string кодирование значения в JSON, объекты хранятся как ассоциативные массивы */
    public const FORMAT_JSON = 'json';

    /** @var string кодирование значения через serialize, объекты сохраняются/восстанавливаются целиком */
    public const FORMAT_SERIALIZE = 'serialize';

    /** @var array форматы кодирования значения */
    public const FORMATS = [
        self::FORMAT_STRING => 'String',
        self::FORMAT_JSON => 'JSON',
        self::FORMAT_SERIALIZE => 'Serialize'
    ];

    /** @var string формат кодирования поля значения */
    public $format = self::FORMAT_JSON;

    /** @var Connection база данных */
    public $db = 'db';

    /** @var string имя таблицы в базе данных */
    public $tableName = '{{settings}}';

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function init() : void
    {
        $this->db = Instance::ensure($this->db, Connection::class);

        if (empty($this->tableName)) {
            throw new InvalidConfigException('tableName');
        }

        if (! array_key_exists($this->format, self::FORMATS)) {
            throw new InvalidConfigException('format');
        }

        $this->initDatabase();
    }

    /**
     * Инициализирует базу данных (создает таблицу).
     *
     * @throws NotSupportedException
     * @throws Exception
     */
    protected function initDatabase() : void
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
    protected function encodeValue($value) : string
    {
        try {
            switch ($this->format) {
                case self::FORMAT_STRING:
                    $encoded = (string)$value;
                    break;

                case self::FORMAT_JSON:
                    $encoded = Json::encode($value);
                    break;

                case self::FORMAT_SERIALIZE:
                    $encoded = serialize($value);
                    break;

                default:
                    throw new InvalidConfigException('неизвестный format: ' . $this->format);
            }
        } catch (Throwable $ex) {
            Yii::error($ex, __METHOD__);
            $encoded = (string)$value;
        }

        return $encoded;
    }

    /**
     * Декодирует значение из базы
     *
     * @param ?string $value
     * @return mixed
     */
    protected function decodeValue(?string $value)
    {
        $decoded = null;

        if ($value !== '' && $value !== null) {
            try {
                switch ($this->format) {
                    case self::FORMAT_STRING:
                        $decoded = $value;
                        break;

                    case self::FORMAT_JSON:
                        $decoded = Json::decode($value);
                        break;

                    case self::FORMAT_SERIALIZE:
                        $decoded = unserialize($value, [
                            'allowed_classes' => true
                        ]);

                        break;

                    default:
                        throw new InvalidConfigException('неизвестный формат: ' . $this->format);
                }
            } catch (Throwable $ex) {
                Yii::warning($ex, __METHOD__);
                $decoded = $value;
            }
        }

        return $decoded;
    }

    /**
     * {@inheritDoc}
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

        $values = array_map(
            fn(string $val) => $this->decodeValue($val),
            $query->column($this->db)
        );

        if (is_array($default)) {
            $values = ArrayHelper::merge($default, $values);
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $module, $name, $value = null) : SettingsStore
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
     * {@inheritDoc}
     */
    public function delete(string $module, string $name = null) : SettingsStore
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
