<?php
namespace dicr\settings;

use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Абстрактное хранилище настроек.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180610
 */
abstract class AbstractSettingsStore extends Component
{
    /**
     * Получает значение настройки/настроек.
     *
     * @param string $module имя модуля/модели
     *
     * @param string $name название настройки
     *  Если пустое, то возвращает ассоциативный массив всех настроек модуля.
     *
     * @param mixed $default значение по-умолчанию.
     *  Если name задано, то значение настройки, иначе асоциативный массив значений по-умолчанию.
     *
     * @throws \dicr\settings\SettingsException
     *
     * @return mixed если name задан то значение настройки, иначе ассоциативный массив всех настроек модуля
     */
    public abstract function get(string $module, string $name = '', $default = null);

    /**
     * Сохраняет значение настройки/настроек.
     *
     * Если значение пустое, то удаляет его.
     *
     * @param string $module название модуля/модели
     * @param string|array $name название параметра или ассоциативный массив параметр => значение
     * @param mixed $value значение если name как скаляр
     * @throws \dicr\settings\SettingsException
     * @return $this
     */
    public abstract function set(string $module, $name, $value = '');

    /**
     * Удалить значение.
     * Значения удаляются методом установки в null.
     *
     * @param string $module название модуля/модели.
     * @param string|null $name название настройки.
     *        Если не задано, то удаляются все настройи модуля.
     * @throws \dicr\settings\SettingsException
     * @return $this
     */
    public abstract function delete(string $module, string $name = '');

    /**
     * Возвращает имя модуля настроек для модели.
     *
     * @param \yii\base\Model $model
     * @throws InvalidArgumentException
     * @return string класс модели
     */
    protected function getModuleName(Model $model)
    {
        if (empty($model)) {
            throw new InvalidArgumentException('model');
        }

        return get_class($model);
    }

    /**
     * Загружает атрибуты модели из базы.
     *
     * @param \yii\base\Model $model модель для загрузки аттрибутов из настроек
     * @param bool $safeOnly только безопасные аттрибуты
     * @throws \dicr\settings\SettingsException
     * @return $this
     */
    public function loadModel(Model $model, bool $safeOnly = true)
    {
        if (empty($model)) {
            throw new InvalidArgumentException('model');
        }

        $module = $this->getModuleName($model);
        $values = $this->get($module);

        $model->setAttributes($values, $safeOnly);

        return $this;
    }

    /**
     * Сохраняет аттрибуты модели в настройах.
     *
     * @param \yii\base\Model $model модель для сохранения аттрибутов в настройки.
     * @throws \dicr\settings\SettingsException
     * @return $this
     */
    public function saveModel(Model $model)
    {
        if (empty($model)) {
            throw new InvalidArgumentException('model');
        }

        $module = $this->getModuleName($model);

        $this->set($module, $model->attributes);

        return $this;
    }
}