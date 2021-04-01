<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.04.21 05:25:21
 */

declare(strict_types = 1);
namespace dicr\settings;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_merge;
use function is_array;

/**
 * Настройки, хранимые в файле PHP.
 *
 * @property array[] $settings все значения всех модулей.
 * @noinspection MissingPropertyAnnotationsInspection
 */
abstract class FileSettingsStore extends Component implements SettingsStore
{
    /** @var string имя файла для сохранения настроек */
    public $filename;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $this->filename = Yii::getAlias($this->filename);
        if (empty($this->filename)) {
            throw new InvalidConfigException('filename');
        }
    }

    /**
     * @inheritDoc
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function get(string $module, string $name = null, $default = null)
    {
        $settings = $this->data();
        if ($name !== null) {
            return $settings[$module][$name] ?? $default;
        }

        /** @noinspection UnnecessaryCastingInspection */
        return array_merge((array)($default ?: []), (array)($settings[$module] ?? []));
    }

    /**
     * @inheritDoc
     */
    public function set(string $module, $name, $value = null): SettingsStore
    {
        $settings = $this->data();
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
            $this->data($settings);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $module, ?string $name = null): SettingsStore
    {
        $settings = $this->data();
        $changed = false;

        if (isset($settings[$module])) {
            if ($name === null) {
                unset($settings[$module]);
                $changed = true;
            } elseif (isset($settings[$module][$name])) {
                unset($settings[$module][$name]);
                $changed = true;
            }
        }

        if ($changed) {
            $this->data($settings);
        }

        return $this;
    }

    /**
     * Загружает настройки из файла.
     *
     * @return array[]
     * @throws Exception
     */
    abstract protected function loadFile(): array;

    /**
     * Сохраняет настройки в файл.
     *
     * @param array[] $settings
     * @return $this
     * @throws Exception
     */
    abstract protected function saveFile(array $settings): self;

    /** @var array */
    private $_data;

    /**
     * Получить/установить данные настроек.
     *
     * @param array|null $settings
     * @return array|array[]
     * @throws Exception
     */
    protected function data(array $settings = null): array
    {
        if ($settings !== null) {
            $this->_data = $settings;
            $this->saveFile($settings);
        } elseif ($this->_data === null) {
            $this->_data = $this->loadFile();
        }

        return $this->_data;
    }
}
