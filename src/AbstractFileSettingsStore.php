<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:33:38
 */

declare(strict_types = 1);
namespace dicr\settings;

use Yii;
use yii\base\InvalidConfigException;
use function is_array;

/**
 * Настройки, хранимые в файле PHP.
 *
 * @noinspection MissingPropertyAnnotationsInspection
 */
abstract class AbstractFileSettingsStore extends AbstractSettingsStore
{
    /** @var string имя файла для сохранения настроек */
    public $filename;

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        parent::init();

        $this->filename = Yii::getAlias($this->filename);
        if (empty($this->filename)) {
            throw new InvalidConfigException('filename');
        }
    }

    /**
     * Загружает настройки из файла
     *
     * @return array
     * @throws \dicr\settings\SettingsException
     */
    abstract protected function loadData();

    /**
     * Сохраняет настройки в файл
     *
     * @param array $settings
     * @return self
     * @throws \dicr\settings\SettingsException
     */
    abstract protected function saveData(array $settings);

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::get()
     */
    public function get(string $module, string $name = null, $default = null)
    {
        $settings = $this->loadData();

        if (isset($name)) {
            return $settings[$module][$name] ?? $default;
        }

        return array_merge((array)($settings[$module] ?? []), (array)($default ?: []));
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::set()
     */
    public function set(string $module, $name, $value = null)
    {
        $settings = $this->loadData();
        $changed = false;

        foreach (is_array($name) ? $name : [$name => $value] as $key => $val) {
            if ($val !== null && $val !== '') {
                $settings[$module][$key] = $val;
                $changed = true;
            } elseif (isset($settings[$module][$key])) {
                unset($settings[$module][$key]);
                $changed = true;
            }
        }

        if ($changed) {
            $this->saveData($settings);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::delete()
     */
    public function delete(string $module, string $name = null)
    {
        $settings = $this->loadData();
        $changed = false;

        if (isset($settings[$module])) {
            if ($name === null || $name === '') {
                unset($settings[$module]);
                $changed = true;
            } elseif (isset($settings[$module][$name])) {
                unset($settings[$module][$name]);
                $changed = true;
            }
        }

        if ($changed) {
            $this->saveData($settings);
        }

        return $this;
    }
}