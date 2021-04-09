<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>[[ $title ]]</title>
</head>

<body>
	@includes ( test001/globalnav )

	<div class="container">
		@content
	</div>
	@while ( $sum > 0 )
	<a href="#">NO:[[ $sum ]]</a>
	<?php $sum -= 5; ?>
	@endwhile

</body>

</html>