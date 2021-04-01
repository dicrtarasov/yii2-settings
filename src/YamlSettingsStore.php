<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.04.21 05:30:59
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
    protected function loadFile(): array
    {
        $settings = [];

        if (file_exists($this->filename)) {
            $settings = yaml_parse_file($this->filename);
            if (! is_array($settings)) {
                throw new Exception('Ошибка загрузки файла: ' . $this->filename);
            }
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    protected function saveFile(array $settings): FileSettingsStore
    {
        if (! yaml_emit_file($this->filename, $settings, YAML_UTF8_ENCODING, YAML_LN_BREAK)) {
            throw new Exception('ошибка сохранения файла: ' . $this->filename);
        }

        return $this;
    }
}
