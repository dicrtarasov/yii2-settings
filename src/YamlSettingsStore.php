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

use yii\base\Exception;

use function is_array;
use function yaml_emit_file;
use function yaml_parse_file;

/**
 * Настройки в Yaml-файле.
 */
class YamlSettingsStore extends FileSettingsStore
{
    /**
     * @inheritDoc
     */
    protected function loadFile() : array
    {
        $settings = [];

        if (file_exists($this->filename)) {
            error_clear_last();

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $settings = @yaml_parse_file($this->filename);
            if (! is_array($settings)) {
                $err = error_get_last();
                throw new Exception('Ошибка загрузки файла: ' . $this->filename . ': ' . $err['message']);
            }
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    protected function saveFile(array $settings) : FileSettingsStore
    {
        error_clear_last();

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        if (! @yaml_emit_file($this->filename, $settings, YAML_UTF8_ENCODING, YAML_LN_BREAK)) {
            $err = error_get_last();
            throw new Exception('ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
        }

        return $this;
    }
}
