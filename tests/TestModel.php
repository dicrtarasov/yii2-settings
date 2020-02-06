<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.02.20 03:20:14
 */

declare(strict_types = 1);
namespace dicr\tests;

use dicr\settings\AbstractSettingsModel;

/**
 * Тестовая модель
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class TestModel extends AbstractSettingsModel
{
    /** @var array набор тестовых данных модели */
    public const TEST_DATA = [
        'null' => null,
        'boolean' => false,
        'zero' => 0,
        'float' => - 1.23,
        'string' => "Иванов Иван\nИванович",
        'array' => [
            1, 2, 'a' => 'b'
        ]
    ];

    public $null;

    public $boolean;

    public $zero;

    public $float;

    public $string;

    public $array;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['null', 'boolean', 'zero', 'float', 'string', 'array'], 'safe']
        ];
    }
}
