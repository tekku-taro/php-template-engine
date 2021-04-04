<?php
namespace Taro\PageMaker\Core;

use Taro\PageMaker\Tasks\Task;
use Taro\PageMaker\Utility\File;

// ファイルファクトリー
class Factory
{

    /**
     * コンパイラ
     *
     * @var ICompiler
     */
    private $compiler;

    /**
     * タスク
     *
     * @var Task
     */
    private $task;

    // 雛形ファイル
    public $template;

    // 保存ファイル
    public $savedFile;

    // 作成されたファイルデータ
    public $compiled;

    public function __construct(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    // 設定ロード
    public function new(Task $task)
    {
        $this->task = $task;
    }

    // メイン処理
    public function process()
    {
        // コンパイル
        $this->compile();
        // データの出力
        $this->dataOutput();
    }

    // コンパイル
    private function compile()
    {
        $this->compiled = $this->compiler->run($this->task->template, $this->task->data);
    }

    // データの出力
    private function dataOutput()
    {
        $this->saveToFile();
        
        if ($this->compiler instanceof ViewCompiler) {
            return	$this->render();
        }
    }

    // レンダリング処理
    private function render()
    {
        extract($this->task->data);
        include_once $this->savedFile;
    }

    // ファイルへ保存
    private function saveToFile()
    {
        $this->savedFile = File::save($this->compiled, $this->task->output);
    }
}
