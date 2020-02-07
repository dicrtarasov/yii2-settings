<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 07:06:03
 */

declare(strict_types = 1);
namespace dicr\settings;

use Throwable;
use Yii;
use function is_array;

/**
 * Настройки, хранимые в файле PHP.
 */
class PhpSettingsStore extends AbstractFileSettingsStore
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
                    $this->_settings = @include($this->filename);
                    if (! is_array($this->_settings)) {
                        $err = error_get_last();
                        throw new SettingsException('Ошибка загрузка файла: ' . $this->filename . ': ' .
                            $err['message']);
                    }
                } catch (Throwable $ex) {
                    Yii::warning($ex->getMessage(), __METHOD__);
                    $this->_settings = [];
                }
            }
        }

        return $this->_settings;
    }

    /**
     * Сохраняет настройки в файл
     *
     * @param array $settings
     * @return self
     */
    protected function saveData(array $settings)
    {
        $this->_settings = $settings;

        try {
            error_clear_last();

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            if (@file_put_contents($this->filename, '<?php return ' . var_export($this->_settings, true) . ';') ===
                false) {
                $err = error_get_last();
                throw new SettingsException('Ошибка сохранения файла: ' . $this->filename . ': ' . $err['message']);
            }
        } catch (Throwable $ex) {
            Yii::error($ex->getMessage(), __METHOD__);
        }

        return $this;
    }
}
