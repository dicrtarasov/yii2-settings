<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 02:57:46
 */

/** @noinspection PhpComposerExtensionStubsInspection */
declare(strict_types = 1);
namespace dicr\settings;

use function yaml_emit_file;
use function yaml_parse_file;

/**
 * Настройки в Yaml-файле.
 */
class YamlSettingsStoreStore extends AbstractFileSettingsStore
{
    /** @var array кэш настроек */
    private $_settings;

    /**
     * Загружает настройки из файла
     *
     * @return array
     * @throws \dicr\settings\SettingsException
     */
    protected function loadData()
    {
        if (! isset($this->_settings)) {
            $this->_settings = [];

            if (file_exists($this->filename)) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection */
                $this->_settings = @yaml_parse_file($this->filename);
                if ($this->_settings === false) {
                    $this->_settings = [];
                    $err = error_get_last();
                    error_clear_last();
                    throw new SettingsException('Ошибка загрузки файла: ' . $this->filename . ': ' . $err['message']);
                }
            }
        }

        return $this->_settings;
    }

    /**
     * Сохраняет настройки в файл
     *
     * @param array $settings to save
     * @return \dicr\settings\YamlSettingsStoreStore
     * @throws SettingsException
     */
    protected function saveData(array $settings)
    {
        $this->_settings = $settings;
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        if (! @yaml_emit_file($this->filename, $this->_settings, YAML_UTF8_ENCODING, YAML_LN_BREAK)) {
            $err = error_get_last();
            error_clear_last();
            throw new SettingsException('ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
        }

        return $this;
    }
}
