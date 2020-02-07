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
 * @property array[] $settings все начения всех модулей.
 * @noinspection MissingPropertyAnnotationsInspection
 */
abstract class AbstractFileSettingsStore extends AbstractSettingsStore
{
    /** @var string имя файла для сохранения настроек */
    public $filename;

    /** @var array */
    private $_settings = [];

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
     * Загружает настройки из файла.
     *
     * @return array[]
     * @throws \dicr\settings\SettingsException
     */
    abstract protected function loadFile();

    /**
     * Сохраняет настройки в файл.
     *
     * @param array[] $settings
     * @return self
     * @throws \dicr\settings\SettingsException
     */
    abstract protected function saveFile(array $settings);

    /**
     * Возвращает все значения всех модулей.
     *
     * @return array[]
     * @throws \dicr\settings\SettingsException
     */
    public function getSettings()
    {
        if (!isset($this->_settings)) {
            $this->_settings = $this->loadFile() ?: [];
        }

        return $this->_settings;
    }

    /**
     * Установить настройки.
     *
     * @param array[] $settings
     * @return $this
     * @throws \dicr\settings\SettingsException
     */
    public function setSettings(array $settings)
    {
        $this->_settings = $settings;
        $this->saveFile($settings);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::get()
     */
    public function get(string $module, string $name = null, $default = null)
    {
        $settings = $this->getSettings();

        if (isset($name)) {
            return $settings[$module][$name] ?? $default;
        }

        /** @noinspection UnnecessaryCastingInspection */
        return array_merge((array)($settings[$module] ?? []), (array)($default ?: []));
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::set()
     */
    public function set(string $module, $name, $value = null)
    {
        $settings = $this->getSettings();
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
            $this->setSettings($settings);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::delete()
     */
    public function delete(string $module, string $name = null)
    {
        $settings = $this->getSettings();
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
            $this->setSettings($settings);
        }

        return $this;
    }
}
