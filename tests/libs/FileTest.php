<?php
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Taro\PageMaker\Utility\File;

class FileTest extends TestCase
{
    public function setUp():void
    {
    }


    public function testRead()
    {
        $path = __DIR__ . '/test.json';
        $file = File::read($path);

        
        $this->assertNotNull($file);
    }

    public function testBuildTemplatePath()
    {
        $path = '\test.php';
        $actual = File::buildTemplatePath($path);
        $expected = 'C:\Users\yasun\OneDrive\ドキュメント\personalProjects\template-engine\src\templates\test.php';
        
        $this->assertEquals($expected, $actual);
    }

    public function testBuildOutputPath()
    {
        $path = '\test.php';
        $actual = File::buildOutputPath($path);
        $expected = 'C:\Users\yasun\OneDrive\ドキュメント\personalProjects\template-engine\src\outputs\test.php';
        
        $this->assertEquals($expected, $actual);
    }
}
