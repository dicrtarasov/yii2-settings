<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:13:24
 */

declare(strict_types = 1);

namespace dicr\settings;

use yii\base\Model;
use yii\di\Instance;

/**
 * Абстрактная модель настроек.
 *
 * Используетс как синглетон через Model::instance()
 */
abstract class AbstractSettingsModel extends Model
{
    /**
     * Загружает модель при создании.
     *
     * {@inheritDoc}
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
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
     * @return \dicr\settings\AbstractSettingsStore
     * @throws \yii\base\InvalidConfigException
     */
    public static function store()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Instance::ensure('settings', AbstractSettingsStore::class);
    }

    /**
     * Загружает настройки из хранилища настроек
     *
     * @param bool $safeOnly только безопасные атрибуты
     * @return $this
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
     */
    public function loadSettings(bool $safeOnly = true)
    {
        static::store()->loadModel($this, $safeOnly);

        return $this;
    }

    /**
     * Сохраняет модель в хранилище настроек
     *
     * @param bool $validate выполнить валидацию
     * @return bool при ошибке валидации возвращает false
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
     */
    public function save(bool $validate = true)
    {
        if ($validate && ! $this->validate()) {
            return false;
        }

        static::store()->saveModel($this);

        return true;
    }
}
