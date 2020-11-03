<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 07:06:03
 */

declare(strict_types = 1);
namespace dicr\settings;

use yii\base\Exception;

use function is_array;

/**
 * Настройки, хранимые в файле PHP.
 */
class PhpSettingsStore extends FileSettingsStore
{
    /**
     * @inheritDoc
     */
    protected function loadFile() : array
    {
        $settings = [];

        if (file_exists($this->filename)) {
            error_clear_last();
            /** @noinspection PhpUsageOfSilenceOperatorInspection, PhpIncludeInspection */
            $settings = @include($this->filename);
            if (! is_array($settings)) {
                $err = error_get_last();
                throw new Exception('Ошибка загрузка файла: ' . $this->filename . ': ' . $err['message']);
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

        $content = '<?php return ' . var_export($settings, true) . ';';

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        if (@file_put_contents($this->filename, $content, LOCK_EX) === false) {
            $err = error_get_last();
            throw new Exception('Ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
        }

        return $this;
    }
}
