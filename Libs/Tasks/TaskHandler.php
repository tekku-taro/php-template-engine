<?php
namespace Taro\PageMaker\Tasks;

use Taro\PageMaker\Core\Factory;
use Taro\PageMaker\Core\ICompiler;
use Taro\PageMaker\Utility\File;

// タスク管理
class TaskHandler
{
    // コンパイラ
    public $compiler;

	/**
	 * キャッシュを使用するか
	 *
	 * @var string load|overwrite|ignore
	 */
	public $cacheMode = 'load';

    // タスクファイル(json) のパス
    public $filePath = __DIR__ . '/tasklist.json';


    // タスクのキュー配列
    private $queues = [];

    // ファイルのロード
    public function load()
    {
        $json = File::read($this->filePath);

        $jsonData = json_decode($json, true);

        if (!empty($jsonData)) {
            foreach ($jsonData as $item) {
                $task = new Task();
                $this->copyToModel($task, $item);
                $this->enqueue($task);
            }
        }
    }

    private function copyToModel(Task $task, array $item)
    {
        $task->name = $item['name'];
        $task->template = $item['template'];
        $task->description = $item['description'];
        $task->data = $item['data'];
        $task->output = $item['output'];
    }

    // キューにタスクを追加
    private function enqueue(Task $task)
    {
        $this->queues[] = $task;
    }

    // キューからタスクを取得
    private function dequeue()
    {
        if (empty($this->queues)) {
            return null;
        }
        $task = array_shift($this->queues);
        return $task;
    }

    // タスクの実行
    public function run()
    {
        $factory = new Factory($this->compiler, $this->cacheMode);
        while (!empty($this->queues)) {
            $task = $this->dequeue();
            $factory->new($task);
            $factory->process();
        }
    }

    // コンパイラのセット
    public function setCompiler(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    // jsonファイルのパスをセット
    public function setJsonPath($filePath)
    {
        $this->filePath =  File::buildPath(__DIR__, File::appendExtension($filePath, 'json'));
    }

    // タスク一覧の表示
    public function show()
    {
        print_r($this->queues);
    }
}
