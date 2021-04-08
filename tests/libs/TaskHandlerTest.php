<?php
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Taro\PageMaker\Core\ViewCompiler;
use Taro\PageMaker\Tasks\TaskHandler;
use Taro\PageMaker\Utility\File;

class TaskHandlerTest extends TestCase
{
    public function setUp():void
    {
    }


    public function testRun()
    {
        $path = __DIR__ . '/tasklist.json';
        $output = File::buildOutputPath('result.php');
        // タスクファイルの読み込み
        $taskHandler = new TaskHandler;
        $taskHandler->filePath = $path;
        $taskHandler->setCompiler(new ViewCompiler);
        $taskHandler->load();

        ob_start();
        $taskHandler->run();
        $rendered = ob_get_clean();

        $this->assertFileExists($output);

        $this->assertNotNull($rendered);
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
