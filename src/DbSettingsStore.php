<?php
namespace dicr\settings;

use dicr\helper\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Json;
use yii\db\Schema;

/**
 * Настройки в базе данных.
 * 
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180610
 */
class DbSettingsStore extends AbstractSettingsStore {
	
	/** @var \yii\db\Connection база данных */
	public $db = 'db';
	
	/** @var string имя таблицы в базе данных */
	public $table = '{{settings}}';
	
	/**
	 * {@inheritDoc}
	 * @see \yii\base\BaseObject::init()
	 */
	public function init() {

		if (is_string($this->db)) {
			$this->db = \Yii::$app->get($this->db, true);
		}
		
		Instance::ensure($this->db, Connection::class);
		
		$this->table = trim($this->table);
		if (empty($this->table)) {
			throw new InvalidConfigException('пустое имя таблицы');
		}
		
		$this->initDatabase();
	}
	
	/**
	 * Инициализарует базу данных
	 */
	protected function initDatabase() {
		$tableName = preg_replace('~(^\{\{\%?)|(\}\}$)~uism', '', $this->table);
		$schema = $this->db->getSchema();
		
		if (!in_array($tableName, $schema->tableNames)) {
			$this->db->createCommand()->createTable($this->table, [
				'module' => Schema::TYPE_STRING.' NOT NULL',
				'name' => Schema::TYPE_STRING.' NOT NULL',
				'value' => Schema::TYPE_TEXT
			])->execute();
			
			$this->db->createCommand()->createIndex('module-name', $this->table, ['module', 'name'], true)->execute();
		}
	}
	
	/**
	 * Кодирует значение для сохранения в базу.
	 *
	 * @param mixed $value значение
	 * @return string строковое значение
	 */
	protected function encodeValue($value) {
		return Json::encode($value);
	}
	
	/**
	 * Декодирует значение из базы
	 *
	 * @param string $value
	 * @return mixed
	 */
	protected function decodeValue(string $value) {
		return Json::decode($value);
	}

	/**
	 * {@inheritDoc}
	 * @see \dicr\settings\AbstractSettingsStore::get()
	 */
	public function get(string $module, string $name='', $default=null) {
		
		$query = (new Query())->select('[[value]]')->from($this->table)->where([
			'module' => $module
		]);
		
		if ($name !== '') {
			// запрос одного значения
			$value = $query->andWhere(['[[name]]' => $name])->limit(1)->scalar($this->db);
			return $value === null || $value === '' ? $default : $this->decodeValue($value);
		} 
		
		// запрос всех значение модели
		$values = [];
		foreach ($query->addSelect('[[name]]')->each(100, $this->db) as $row) {
			$values[$row['name']] = $this->decodeValue($row['value']);
		}
			
		if (is_array($default)) {
			$values = ArrayHelper::merge($default, $values);
		}
		
		return $values;
	}

	/**
	 * {@inheritDoc}
	 * @see \dicr\settings\AbstractSettingsStore::set()
	 */
	public function set(string $module, $name, $value='') {
		$values = [];
		if (is_array($name)) {
			$values = $name;
		} else {
			$values[$name] = $value;
		}
		
		foreach ($values as $name => $value) {
			$value = (string)$this->encodeValue($value);
			
			if ($value == '') {
				$this->delete($module, $name);
			} else {
				// для совместимости вместо REPLACE используем delete/indert
				$this->db->createCommand()->delete($this->table, [
					'module' => $module,
					'name' => $name
				])->execute();
				
				$this->db->createCommand()->insert($this->table, [
					'module' => $module,
					'name' => $name,
					'value' => $value
				])->execute();
			}
		}
		
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @see \dicr\settings\AbstractSettingsStore::delete()
	 */
	public function delete(string $module, string $name='') {

		$conds = [
			'module' => $module
		];
		
		if ($name != '') {
			$conds['name'] = $name;
		}
		
		$this->db->createCommand()->delete($this->table, $conds)->execute();

		return $this;
	}
}
