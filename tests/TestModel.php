<?php
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
	const DATA = [
		'null' => null,
		'boolean' => false,
		'zero' => 0,
		'float' => -1.23,
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

	public function rules()
	{
	    return [
            [['null', 'boolean', 'zero', 'float', 'string', 'array'], 'safe']
	    ];
	}
}
