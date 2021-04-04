<?php
require_once "vendor/autoload.php";

use Taro\PageMaker\Core\CodeMaker;
use Taro\PageMaker\Tasks\TaskHandler;

// タスクファイルの読み込み
$taskHandler = new TaskHandler;
$taskHandler->setJsonPath('tasklist_file');
$taskHandler->setCompiler(new CodeMaker);
$taskHandler->load();

// タスク一覧の表示
// $taskHandler->show();
$taskHandler->run();
