<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 20:59:50
 */

declare(strict_types = 1);

namespace dicr\settings;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Абстрактная модель настроек.
 *
 * Используется как singleton через Model::instance()
 */
abstract class AbstractSettingsModel extends Model
{
    /**
     * @inheritDoc
     * @throws SettingsException
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        // загружаем настройки
        $this->loadSettings();
    }

    /**
     * Возвращает хранилище настроек.
     * Для переопределения в дочерних реализациях.
     *
     * @return AbstractSettingsStore
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function store()
    {
        return Yii::$app->get('settings');
    }

    /**
     * Возвращает название раздела настроек в котором хранятся аттрибуты этой модели.
     *
     * @return string
     */
    public static function module()
    {
        return static::class;
    }

    /**
     * Загружает настройки из хранилища настроек
     *
     * @param bool $safeOnly только безопасные атрибуты
     * @return $this
     * @throws SettingsException
     * @throws InvalidConfigException
     */
    public function loadSettings(bool $safeOnly = true)
    {
        $store = static::store();
        $module = static::module();
        $values = $store->get($module);
        $this->setAttributes($values, $safeOnly);

        return $this;
    }

    /**
     * Сохраняет модель в хранилище настроек
     *
     * @param bool $validate выполнить валидацию
     * @return bool при ошибке валидации возвращает false
     * @throws SettingsException
     * @throws InvalidConfigException
     */
    public function save(bool $validate = true)
    {
        if ($validate && ! $this->validate()) {
            return false;
        }

        $store = static::store();
        $module = static::module();
        $store->set($module, $this->attributes);

        return true;
    }
}
