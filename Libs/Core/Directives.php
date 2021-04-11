<?php
namespace Taro\PageMaker\Core;

use ErrorException;

class Directives
{
	public static $list = [
		'var' =>[
			'begin_h' => '\[\[',
			'end_h' => '\]\]',
			'begin' => '\[%',
			'end' => '%\]',
		],
		'content' =>'@content',
		'if' =>[
			'begin' => '@if',
			'end' => '@endif',
			'else' => '@else',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'elseif' =>[
			'begin' => '@elseif',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'for' =>[
			'begin' => '@for',
			'end' => '@endfor',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'while' =>[
			'begin' => '@while',
			'end' => '@endwhile',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'foreach' =>[
			'begin' => '@foreach',
			'end' => '@endforeach',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'includes' =>[
			'begin' => '@includes',
			'condbegin' => '\(',
			'condend' => '\)',
		],
		'extends' =>[
			'begin' => '@extends',
			'condbegin' => '\(',
			'condend' => '\)',
		],
	];

	public static function symbol($symbol) 
	{
		$keys = explode('.', $symbol);
		$list = self::$list;
		return self::findElem($symbol, $list, $keys);
	}

	public static function findElem($symbol, $array, $keys)
	{
		$key = array_shift($keys);
		if(!isset($array[$key])) {
			throw new ErrorException($symbol. 'に対応するディレクティブが登録されていません。');
		}

		if(count($keys) === 0) {
			return $array[$key];
		}else{
			return self::findElem($symbol, $array[$key], $keys);
		}

	}


}

