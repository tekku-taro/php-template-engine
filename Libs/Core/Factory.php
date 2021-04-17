<?php
namespace Taro\PageMaker\Core;

use Taro\PageMaker\Tasks\Task;
use Taro\PageMaker\Utility\File;

// ファイルファクトリー
class Factory
{
	use HelperTrait;

    /**
     * コンパイラ
     *
     * @var ICompiler
     */
    private $compiler;

	/**
	 * コンパイラのタイプ
	 *
	 * @var string view|codemake
	 */
	private $compilerType;

	/**
	 * キャッシュを使用するか
	 *
	 * @var string load|overwrite|ignore
	 */
	public $cacheMode = 'load';

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

    public function __construct(ICompiler $compiler, $cacheMode)
    {
		$this->cacheMode = $cacheMode;
        $this->compiler = $compiler;

        if ($this->compiler instanceof ViewCompiler) {
            $this->compilerType = 'view';
        } elseif ($this->compiler instanceof CodeMaker) {
			$this->compilerType = 'codemake';
		}
    }
    
    // 設定ロード
    public function new(Task $task)
    {
        $this->task = $task;
    }

    // メイン処理
    public function process()
    {
		switch ($this->cacheMode) {
			case 'load':
				$cache = File::loadCache($this->task->template);
				if($cache === false) {
					$this->compile();
					File::saveCache($this->task->template, $this->compiled);
				}else{
					$this->compiled = $cache;
				}				
				break;
			case 'overwrite':
				$this->compile();
				File::saveCache($this->task->template, $this->compiled);				
				break;
			case 'ignore':
				$this->compile();
				break;
		}
		
        // データの出力
        $this->dataOutput();
    }

    // コンパイル
    private function compile()
    {
		$this->compiled = $this->compiler->run($this->task->template, $this->task->data);
		if(!empty($this->compiler->data)) {
			$this->task->data += $this->compiler->data;
		}
	}

    // データの出力
    private function dataOutput()
    {
		
		if ($this->compilerType  === 'view') {
            switch ($this->cacheMode) {
                case 'load':
                case 'overwrite':
                    $this->savedFile = File::checkCache($this->task->template);
					break;
                case 'ignore':
					$this->saveToFile();
                    break;
                }			
			return	$this->render();
        }else {
			$this->saveToFile();
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
