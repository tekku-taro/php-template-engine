<?php
require_once "vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Taro\PageMaker\Core\ViewCompiler;

class ViewCompilerTest extends TestCase
{
    public function setUp():void
    {
    }



	// includes限界テスト
	public function testIncludesToLimit()
	{
        $path = '\test001\includes\index.php';
        $compiler = new ViewCompiler();
		$compiler->minLevel = -3;
        $actual = $compiler->run($path, []);

		$expected = 
'<h1>Includes Test</h1>
<h2>Level1</h2>
<h2>Level2</h2>
<h2>Level3</h2>';

        $this->assertEquals($this->uLineEnd($expected),$this->uLineEnd($actual));		
	}

	public function testIncludesOverLimit()
	{
        $path = '\test001\includes\index.php';
        $compiler = new ViewCompiler();
		$compiler->minLevel = -2;
		try {
			$actual = $compiler->run($path, []);
		} catch (ErrorException $ex) {
			$expected = 'Includeは2レベルまでが上限です。';
			$this->assertEquals($expected, $ex->getMessage());
		}

	}

	// extends 限界テスト
	public function testExtendsToLimit()
	{
        $path = '\test001\extends\index.php';
        $compiler = new ViewCompiler();
		$compiler->maxLevel = 3;
        $actual = $compiler->run($path, []);
		print $actual;
		$expected = 
'<h2>Level3</h2>
<h2>Level2</h2>
<h2>Level1</h2>
<h1>Extends Test</h1>';

        $this->assertEquals($this->uLineEnd($expected),$this->uLineEnd($actual));		
	}

	public function testExtendsOverLimit()
	{
        $path = '\test001\extends\index.php';
        $compiler = new ViewCompiler();
		$compiler->maxLevel = 2;
		try {
			$actual = $compiler->run($path, []);
		} catch (ErrorException $ex) {
			$expected = '継承は2回までが上限です。';
			$this->assertEquals($expected, $ex->getMessage());
		}

	}


    public function testRun_viewcompiler()
    {
        $path = '\test001\index.php';
        $compiler = new ViewCompiler();
        $actual = $compiler->run($path, []);
		print PHP_EOL. $actual;
        $expected =
'<?php 
$title=\'書籍一覧\';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?></title>
</head>

<body>
	<div class="globalnav">
	<nav>
		<ul>
			<?php foreach($menus as $href => $menu): ?>
			<li><a href="<?= $href ?>"><?= $menu ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>

	<div class="container">
		<div class="test-if">
	<?php if($sum < 10): ?> <p><?= $this->h($sum) ?> は 10より小さい</p>
		<?php elseif($sum> 10 && $sum < 20): ?> <p><?= $this->h($sum) ?> は 10より大きく20より小さい</p>
			<?php else: ?>
			<p><?= $this->h($sum) ?> は 20より大きい</p>
			<?php endif; ?>
</div>
<div class="test-for">
	<div class="btn-group">
		<?php for($i = 0; $i < 10; $i++): ?> <input class="btn btn-default" type="button" value="<?= $i ?>">
			<?php endfor; ?>
	</div>
</div>

<div class="test-sanitize">
	<?= $this->h(\'<bold>BOLD</bold>\') ?>
</div></div>

</body>

</html>';


        $this->assertEquals($this->uLineEnd($expected),$this->uLineEnd($actual));
    }

	private function uLineEnd($text)
	{
		return str_replace("\r\n", "\n", $text);//
	}
}
