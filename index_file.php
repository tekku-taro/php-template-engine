<?php
require_once "vendor/autoload.php";

use Taro\PageMaker\Core\CodeMaker;
use Taro\PageMaker\Tasks\TaskHandler;

// タスクファイルの読み込み
$taskHandler = new TaskHandler;
$taskHandler->cacheMode = 'ignore';
$taskHandler->setCompiler(new CodeMaker);
$taskHandler->setJsonPath('tasklist');
$taskHandler->load();

// タスク一覧の表示
// $taskHandler->show();
$taskHandler->run();
