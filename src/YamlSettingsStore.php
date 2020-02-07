<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 07:04:36
 */

/** @noinspection PhpComposerExtensionStubsInspection */
declare(strict_types = 1);
namespace dicr\settings;

use Throwable;
use Yii;
use function yaml_emit_file;
use function yaml_parse_file;

/**
 * Настройки в Yaml-файле.
 */
class YamlSettingsStore extends AbstractFileSettingsStore
{
    /** @var array кэш настроек */
    private $_settings;

    /**
     * Загружает настройки из файла
     *
     * @return array
     */
    protected function loadData()
    {
        if (! isset($this->_settings)) {
            $this->_settings = [];

            if (file_exists($this->filename)) {
                try {
                    error_clear_last();

                    /** @noinspection PhpUsageOfSilenceOperatorInspection */
                    $this->_settings = @yaml_parse_file($this->filename);
                    if ($this->_settings === false) {
                        $err = error_get_last();
                        throw new SettingsException('Ошибка загрузки файла: ' . $this->filename . ': ' .
                            $err['message']);
                    }
                } catch (Throwable $ex) {
                    $this->_settings = [];
                    Yii::warning($ex->getMessage(), __METHOD__);
                }
            }
        }

        return $this->_settings;
    }

    /**
     * Сохраняет настройки в файл
     *
     * @param array $settings to save
     * @return \dicr\settings\YamlSettingsStore
     */
    protected function saveData(array $settings)
    {
        $this->_settings = $settings;

        try {
            error_clear_last();

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            if (! @yaml_emit_file($this->filename, $this->_settings, YAML_UTF8_ENCODING, YAML_LN_BREAK)) {
                $err = error_get_last();
                error_clear_last();
                throw new SettingsException('ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
            }
        } catch (Throwable $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
        }

        return $this;
    }
}
