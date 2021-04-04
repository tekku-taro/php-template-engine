<?php
namespace Taro\PageMaker\Core;

// コンパイラ インターフェース
interface ICompiler
{
    // 雛形からファイルデータを作成
    public function run($template, array $data);
}
