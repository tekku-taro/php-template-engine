<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>書籍一覧</title>
	<?php
    echo 'echo test layout';
    foreach ($styles as $style) {
        echo $style;
    }
?>	
</head>

<body>
	<div class="globalnav">
	<nav>
		<ul>
						<li><a href="http:://mysite1">MySite1</a></li>
						<li><a href="http:://mysite2">MySite2</a></li>
						<li><a href="http:://mysite3">MySite3</a></li>
					</ul>
	</nav>
	<?php
    echo 'echo test nav';
    foreach ($links as $link) {
        echo $link;
    }
?>

</div>

	<div class="container">
		<div class="test-if">
				<p>50 は 20より大きい</p>
			</div>
<?php
 echo 'echo test';
 foreach ($team as $member) {
     echo $member;
 }
?>
<div class="test-for">
	<div class="btn-group">
		 <input class="btn btn-default" type="button" value="0">
			 <input class="btn btn-default" type="button" value="1">
			 <input class="btn btn-default" type="button" value="2">
			 <input class="btn btn-default" type="button" value="3">
			 <input class="btn btn-default" type="button" value="4">
			 <input class="btn btn-default" type="button" value="5">
			 <input class="btn btn-default" type="button" value="6">
			 <input class="btn btn-default" type="button" value="7">
			 <input class="btn btn-default" type="button" value="8">
			 <input class="btn btn-default" type="button" value="9">
				</div>
</div></div>

</body>

</html>