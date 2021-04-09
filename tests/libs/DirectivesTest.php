<?php
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Taro\PageMaker\Core\Directives;


class DirectivesTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		Directives::$list = [
			'test' =>[
				'key' => 'var',
				'array' => [
					'key2' => 'data'
				]
			],
			'test2' =>'var2',
		];
	}

    public function setUp():void
    {
    }


    public function testSymbol()
    {
		$this->assertEquals('var', Directives::symbol('test.key'));
		$this->assertEquals('data', Directives::symbol('test.array.key2'));
		$this->assertEquals('var2', Directives::symbol('test2'));
    }

}
