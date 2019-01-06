<?php
namespace dicr\settings;

use dicr\helper\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Настройки в PHP-файле.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180610
 */
class PhpSettingsStore extends AbstractSettingsStore
{
    /** @var string имя файла для сохранения настроек */
    public $filename;

    /** @var array кэш настроек */
    private $settings;

    /**
     * {@inheritdoc}
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        $this->filename = \Yii::getAlias($this->filename);
        if (empty($this->filename)) {
            throw new InvalidConfigException('filename');
        }
    }

    /**
     * Загружает настройки из файла
     *
     * @return array
     */
    protected function loadData()
    {
        if (! isset($this->settings)) {
            if (@file_exists($this->filename)) {
                $this->settings = @include ($this->filename);
            } else {
                $this->settings = [];
            }
        }
        return $this->settings;
    }

    /**
     * Сохраняет настройки в файл
     *
     * @throws SettingsException
     * @return self
     */
    protected function saveData(array $settings)
    {
        $this->settings = $settings;
        if (! @file_put_contents($this->filename, '<?php return ' . var_export($this->settings, true) . ';')) {
            throw new SettingsException('ошибка сохранения файла: ' . $this->filename);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::get()
     */
    public function get(string $module, string $name = '', $default = null)
    {
        $settings = $this->loadData();

        if ($name != '') {
            return ArrayHelper::getValue($settings, [$module,$name], $default);
        }

        $ret = $settings[$module] ?? [];
        if (is_array($default)) {
            $ret = ArrayHelper::merge($default, $ret);
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::set()
     */
    public function set(string $module, $name, $value = '')
    {
        $values = [];
        if (is_array($name)) {
            $values = $name;
        } else {
            $values[$name] = $value;
        }

        $settings = $this->loadData();
        foreach ($values as $name => $value) {
            if ($value === '' || $value === null) {
                ArrayHelper::remove($settings, [$module,$name]);
            } else {
                $settings[$module][$name] = $value;
            }
        }

        $this->saveData($settings);
        return $this;
    }

    /**
     * {@inheritdoc}
     * @see \dicr\settings\AbstractSettingsStore::delete()
     */
    public function delete(string $module, string $name = '')
    {
        $settings = $this->loadData();

        if ($name == '') {
            unset($settings[$module]);
        } else {
            ArrayHelper::remove($settings, [$module,$name]);
        }

        $this->saveData($settings);
        return $this;
    }
}
