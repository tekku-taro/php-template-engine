# Php Template Engine

フレームワークなどで利用するテンプレートエンジンライブラリです。 あらかじめ用意したテンプレートファイルにデータを埋め込み、ウェブページとして出力できます。

## 特徴

- 基本はView作成用のテンプレートエンジンであり、また設定ファイルを用意することで雛形からPHPファイルを作成できる。
- テンプレートファイルは、他のファイルをインクルードしたり、レイアウトファイルを継承できる。
- 簡易なテンプレート構文を使い、簡潔にテンプレートを記述できる。
- テンプレート構文は使う記号を容易に変更可能。
- PHPファイル作成に使う場合は、jsonファイルに複数の設定を記述することで、一度に多数のファイルを作成できる。

## ディレクトリ構造

```
src
├── libs					　　# ライブラリ本体
│   ├── Core					# ライブラリのコアファイル群
│   └── Tasks					# ファイル作成作業（タスク）の管理
│   │   └── tasklist.json	　　　# ファイル作成作業（タスク）を指定するJsonファイル
│   └── Utility				　 　# ユーティリティ（ファイル操作用クラス）
├── outputs
├── runtime
│   └── views       			 # テンプレートのキャッシュを保存
├── templates       			 # テンプレートフォルダ
│   ... etc
... etc
└── index.php
```



## 使い方（PHPファイルの作成）

### テンプレートファイルを用意

/template フォルダにテンプレートファイル(php) を配置。

### テンプレート構文で記述

**注：CodeMakerコンパイラの場合は、素のPHPコードは実行されません。**

例では、以下のコードのsum.phpファイルを /templateに配置する。

```php
<div class="test-if">
	@if ( $sum < 10 ) <p>[[ $sum ]] は 10より小さい</p>
		@elseif ($sum> 10 && $sum < 20 ) <p>[[ $sum ]] は 10より大きく20より小さい</p>
			@else
			<p>[[ $sum ]] は 20より大きい</p>
			@endif
</div>
```

### ファイル作成作業をJsonファイルにまとめる

/libs/Tasks/ フォルダにある tasklist.json ファイルを編集して使う

配列に一つづつタスクを追加

- name: タスク名
- template: 使用するテンプレートファイル
- description: タスク詳細
- data: 埋め込みたいデータ（キーがテンプレートで使う変数名）
- output: 出力するファイル名

```php
[
	{
		  "name" : "sum test",
		  "template" : "sum.php",
		  "description" : "Sum テスト",
		  "data" : { 
			 "sum" : 50, 
		  },
		  "output" : "result.php"
	}
]
```

### タスク管理クラスを使ってタスクを実行

/index_file.php を参照。

```php
<?php
require_once "vendor/autoload.php";

use Taro\PageMaker\Core\CodeMaker;
use Taro\PageMaker\Tasks\TaskHandler;

$taskHandler = new TaskHandler;
// コンパイラの選択
$taskHandler->setCompiler(new CodeMaker);
// キャッシュを使うか
$taskHandler->cacheMode = 'ignore';
// タスクファイルの読み込み
$taskHandler->setJsonPath('tasklist');
$taskHandler->load();

// タスク一覧の表示
// $taskHandler->show();
// タスク実行
$taskHandler->run();
```

### 作成ファイルの確認

jsonファイルで指定されたファイル名で、/outputフォルダに保存されています。



## フレームワークでViewテンプレートエンジンとして利用

```php
$path = '/sum'; // .php は省略できる
// Viewクラスを使用
$view = new View();
// renderメソッドにテンプレートのパスと渡すデータを指定
$view->render($path, [
    "sum" => 50
]);
```



## テンプレート構文

使われる記号はDirectivesクラスの $list内で変更可能。

```php
// 変数の出力 
[[ $var ]]

// レイアウトファイルでの継承先コンテンツの埋め込み
@content

// if文
@if( $var == true )
@elseif( $var == false )
@else
@endif

// for 文
@for( $i=0;$i < 10; $i++ )
@endfor

// foreach 文
@foreach( $array as $item )
@endforeach

// while 文
@while( $var < 10 )
@endwhile

// includes ファイルのインクルード （.phpは省略）
@includes( view.navbar )

// extends レイアウトファイル （.phpは省略）
@extends( layouts.app )
```





## ライセンス (License)

**Php Template Engine**は[MIT license](https://opensource.org/licenses/MIT)のもとで公開されています。

**Php Template Engine** is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).# Php Template Engine