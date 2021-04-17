<?php
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Taro\PageMaker\Core\CodeMaker;

class CodeMakerTest extends TestCase
{
    public function setUp():void
    {
    }


    public function testRun_codemaker()
    {
        $path = '\test002\index.php';
        $compiler = new CodeMaker();
		$data = [
			"sum" => 50,  
			"menus" => [
				"http:://mysite1"=> "MySite1",
				"http:://mysite2"=> "MySite2",
				"http:://mysite3"=> "MySite3"
			]			
		];
        $actual = $compiler->run($path, $data);
		print $actual;
        $expected = file_get_contents(__DIR__ . '\result_file.php');


        $this->assertEquals($this->uLineEnd($expected),$this->uLineEnd($actual));
    }




	private function uLineEnd($text)
	{
		return str_replace("\r\n", "\n", $text);
	}
}
