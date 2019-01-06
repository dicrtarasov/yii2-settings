<?php
namespace dicr\tests;

use yii\base\Model;

/**
 * Тестовая модель
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class TestModel extends Model
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
}
