<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 01:43:14
 */

declare(strict_types = 1);
namespace dicr\settings;

use yii\base\Component;

/**
 * Абстрактное хранилище настроек.
 *
 * @noinspection MissingPropertyAnnotationsInspection
 */
abstract class AbstractSettingsStore extends Component
{
    /**
     * Получает значение настройки/настроек.
     *
     * @param string $module имя модуля/модели
     *
     * @param string|null $name название настройки
     *  Если пустое, то возвращает ассоциативный массив всех настроек модуля.
     *
     * @param mixed $default значение по-умолчанию.
     *  Если name задано, то значение настройки, иначе асоциативный массив значений по-умолчанию.
     *
     * @return mixed если name задан то значение настройки, иначе ассоциативный массив всех настроек модуля
     * @throws \dicr\settings\SettingsException
     *
     */
    abstract public function get(string $module, string $name = null, $default = null);

    /**
     * Сохраняет значение настройки/настроек.
     * Если значение пустое, то удаляет его.
     *
     * @param string $module название модуля/модели
     * @param string|array $name название параметра или ассоциативный массив параметр => значение
     * @param mixed $value значение если name как скаляр
     * @return $this
     * @throws \dicr\settings\SettingsException
     */
    abstract public function set(string $module, $name, $value = null);

    /**
     * Удалить значение.
     * Значения удаляются методом установки в null.
     *
     * @param string $module название модуля/модели.
     * @param string|null $name название настройки.
     *        Если не задано, то удаляются все настройи модуля.
     * @return $this
     * @throws \dicr\settings\SettingsException
     */
    abstract public function delete(string $module, string $name = null);
}
