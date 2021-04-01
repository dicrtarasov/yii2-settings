<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.04.21 05:33:20
 */

declare(strict_types = 1);

namespace dicr\settings;

use Yii;
use yii\base\Exception;
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
     * @throws Exception
     */
    public function init() : void
    {
        parent::init();

        // загружаем настройки
        $this->loadSettings();
    }

    /**
     * Возвращает хранилище настроек.
     * Для переопределения в дочерних реализациях.
     *
     * @return SettingsStore
     * @throws InvalidConfigException
     */
    public static function store() : SettingsStore
    {
        return Yii::$app->get('settings');
    }

    /**
     * Возвращает название раздела настроек в котором хранятся аттрибуты этой модели.
     *
     * @return string
     */
    public static function module() : string
    {
        return static::class;
    }

    /**
     * Загружает настройки из хранилища настроек
     *
     * @param bool $safeOnly только безопасные атрибуты
     * @return $this
     * @throws Exception
     */
    public function loadSettings(bool $safeOnly = true) : AbstractSettingsModel
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
     * @throws Exception
     */
    public function save(bool $validate = true) : bool
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
