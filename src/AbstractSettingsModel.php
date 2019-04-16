<?php
namespace dicr\settings;

use yii\base\Model;

/**
 * Абстрактная модель настроек.
 *
 * Используетс как синглетон через Model::instance()
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
abstract class AbstractSettingsModel extends Model
{
    /**
     * Закрытый конструктор
     *
     * @param array $config
     */
    protected final function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Загружает модель при создании.
     *
     * {@inheritDoc}
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        $this->loadSettings();
    }

    /**
     * Возвращает хранилище настроек
     *
     * @return \dicr\settings\AbstractSettingsStore
     */
    public static function settingsStore()
    {
        return \Yii::$app->settings;
    }

    /**
     * Загружает настройки из хранилища настроек
     *
     * @param bool $safeOnly только безопасные атрибуты
     * @return $this
     */
    public function loadSettings(bool $safeOnly = true)
    {
        static::settingsStore()->loadModel($this, $safeOnly);

        return $this;
    }

    /**
     * Сохраняет модель в хранилище настроек
     *
     * @return $this
     */
    public function saveSettings()
    {
        static::settingsStore()->saveModel($this);

        return $this;
    }
}