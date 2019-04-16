<?php
namespace dicr\settings;

use yii\base\Model;

/**
 * Абстрактная модель настроек.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
abstract class AbstractSettingsModel extends Model
{
    /** @var static[] экземпляры моделей настроек */
    private static $_instances = [];

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
     * Возвращает экземпляр модели.
     *
     * При создании экземпляра вызывает загрузку данных.
     *
     * @return static
     */
    public final static function instance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            $instance = new static();
            $instance->loadSettings();
            self::$_instances[$class] = $instance;
        }

        return self::$_instances[$class];
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