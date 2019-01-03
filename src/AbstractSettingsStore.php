<?php 
namespace dicr\settings;

use yii\base\Component;
use yii\base\Model;

/**
 * Фбстрактные настройки.
 * 
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180610
 */
abstract class AbstractSettingsStore extends Component {
	
	/**
	 * Получает значение.
	 *
	 * @param string $module имя модуля/модели
	 *
	 * @param string|null $name название настройки.
	 * 		Если пустое, то возвращает ассоциативный массив всех значений для модуля.
	 *
	 * @param mixed|array $default значение по-умолчанию.
	 * 		Если name задано, то значение настройки, иначе массив значений
	 *
	 * @throws \dicr\settings\SettingsException
	 * @return mixed если name задан то значение настройки, иначе массив всех настроек модуля key => val
	 */
	public abstract function get(string $module, string $name='', $default=null);
	
	/**
	 * Сохраняет значение.
	 * Если значение пустое, то удаляет его.
	 *
	 * @param string $module название модуля/модели
	 * @param string|array $name название параметра или ассоциативный массив параметр => значение 
	 * @param mixed $value значение
	 * @throws \dicr\settings\SettingsException
	 * @return self
	 */
	public abstract function set(string $module, $name, $value='');
	
	/**
	 * Удалить значение.
	 * Значения удаляются методом установки в null.
	 *
	 * @param string $module название модуля/модели.
	 *
	 * @param string|null $name название настройки.
	 * 		Если не задано, то удаляются все настройи модуля.
	 *
	 * @throws \dicr\settings\SettingsException
	 * @return self
	 */
	public abstract function delete(string $module, string $name='');
	
	/**
	 * Загружает атрибуты модели из базы.
	 * Имя модуля берется из Model::formName()
	 *
	 * @param Model $model модель для загрузки аттрибутов из настроек
	 *
	 * @throws \dicr\settings\SettingsException
	 * @return bool результат вызова Model::load
	 */
	public function load(Model $model) {
		return $model->setAttributes($this->get($model->formName()), false);
	}
	
	/**
	 * Сохраняет аттрибуты модели в настройах.
	 *
	 * @param Model $model модель для сохранения аттрибутов в настройки.
	 * @throws \dicr\settings\SettingsException
	 * @return self
	 */
	public function save(Model $model) {
		$this->set($model->formName(), $model->getAttributes());
	}
}