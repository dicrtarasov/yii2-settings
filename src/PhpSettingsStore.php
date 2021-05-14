<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license GPL-3.0-or-later
 * @version 14.05.21 23:13:37
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
    protected function loadFile(): array
    {
        $settings = [];

        if (file_exists($this->filename)) {
            /** @noinspection PhpIncludeInspection */
            $settings = include($this->filename);
            if (! is_array($settings)) {
                throw new Exception('Ошибка загрузка файла: ' . $this->filename);
            }
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    protected function saveFile(array $settings): FileSettingsStore
    {
        $content = '<?php return ' . var_export($settings, true) . ';';

        if (file_put_contents($this->filename, $content, LOCK_EX) === false) {
            throw new Exception('Ошибка сохранения файла: ' . $this->filename);
        }

        return $this;
    }
}
