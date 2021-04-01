<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 01.04.21 05:30:02
 */

declare(strict_types = 1);
namespace dicr\settings;

use yii\base\Exception;

use function is_array;
use function serialize;

/**
 * Настройки, хранимые в файле PHP.
 */
class SerializeSettingsStore extends FileSettingsStore
{
    /**
     * @inheritDoc
     */
    protected function loadFile(): array
    {
        $settings = [];

        if (file_exists($this->filename)) {
            $content = file_get_contents($this->filename);
            if ($content === false) {
                throw new Exception('Ошибка загрузка файла: ' . $this->filename);
            }

            $settings = unserialize($content, [
                'allowed_classes' => true
            ]);

            if (! is_array($settings)) {
                throw new Exception('Ошибка декодирования данных: ' . $this->filename);
            }
        }

        return $settings;
    }

    /**
     * @inheritDoc
     */
    protected function saveFile(array $settings): FileSettingsStore
    {
        $content = serialize($settings);
        if (file_put_contents($this->filename, $content, LOCK_EX) === false) {
            throw new Exception('Ошибка сохранения файла: ' . $this->filename);
        }

        return $this;
    }
}
