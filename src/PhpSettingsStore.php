<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 04.07.20 20:59:50
 */

declare(strict_types = 1);
namespace dicr\settings;

use function is_array;

/**
 * Настройки, хранимые в файле PHP.
 */
class PhpSettingsStore extends AbstractFileSettingsStore
{
    /**
     * Загружает настройки из файла
     *
     * @return array
     * @throws SettingsException
     */
    protected function loadFile()
    {
        $settings = [];

        if (file_exists($this->filename)) {
            error_clear_last();
            /** @noinspection PhpUsageOfSilenceOperatorInspection, PhpIncludeInspection */
            $settings = @include($this->filename);
            if (! is_array($settings)) {
                $err = error_get_last();
                throw new SettingsException('Ошибка загрузка файла: ' . $this->filename . ': ' . $err['message']);
            }
        }

        return $settings;
    }

    /**
     * Сохраняет настройки в файл
     *
     * @param array $settings
     * @return $this
     * @throws SettingsException
     */
    protected function saveFile(array $settings)
    {
        error_clear_last();

        $content = '<?php return ' . var_export($settings, true) . ';';

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        if (@file_put_contents($this->filename, $content, LOCK_EX) === false) {
            $err = error_get_last();
            throw new SettingsException('Ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
        }

        return $this;
    }
}
