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
	// extends限界テスト

    public function testRun_viewcompiler()
    {
        $path = '\test001\index.php';
        $compiler = new ViewCompiler();
        $actual = $compiler->run($path, []);

        $expected =
'<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?></title>
</head>

<body><div class="globalnav">
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
	<?php if($sum < 10): ?> <p><?= $sum ?> は 10より小さい</p>
		<?php elseif($sum> 10 && $sum < 20): ?> <p><?= $sum ?> は 10より大きく20より小さい</p>
			<?php else: ?>
			<p><?= $sum ?> は 20より大きい</p>
			<?php endif; ?>
</div>
<div class="test-for">
	<div class="btn-group">
		<?php for($i = 0; $i < 10; $i++): ?> <input class="btn btn-default" type="button" value="<?= $i ?>">
			<?php endfor; ?>
	</div>
</div>
	</div>

</body>

</html>';


        $this->assertEquals($this->uLineEnd($expected),$this->uLineEnd($actual));
    }

	private function uLineEnd($text)
	{
		return str_replace("\r\n", "\n", $text);
	}
}