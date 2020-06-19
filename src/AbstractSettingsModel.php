<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 19.06.20 08:01:44
 */

declare(strict_types = 1);

namespace dicr\settings;

use Yii;
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
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
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
     * @throws \dicr\settings\SettingsException
     * @throws \yii\base\InvalidConfigException
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
