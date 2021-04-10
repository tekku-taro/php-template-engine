<?php
namespace Taro\PageMaker\Core;

use Taro\PageMaker\Core\Factory;
use Taro\PageMaker\Core\ViewCompiler;
use Taro\PageMaker\Tasks\Task;

// View出力クラス
class View
{

	/**
	 * キャッシュを使用するか
	 *
	 * @var string load|overwrite|ignore
	 */
	public $cacheMode = 'load';


    // タスク作成
    public function createTask($template, $data)
    {
		$task = new Task();
        $task->name = 'View rendering';
        $task->template = $template;
        $task->description = 'rendering view from ' . $template;
        $task->data = $data;
        $task->output = 'viewOutput.php';

		return $task;
    }

    // タスクの実行
    public function render($template, $data)
    {
        $factory = new Factory(new ViewCompiler, $this->cacheMode);
		$task = $this->createTask($template, $data);
		$factory->new($task);
		$factory->process();
    }


}
