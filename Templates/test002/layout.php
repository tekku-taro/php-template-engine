<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>[% $title %]</title>
	<?php
    echo 'echo test layout';
    foreach ($styles as $style) {
        echo $style;
    }
?>	
</head>

<body>
	@includes ( test002/globalnav )

	<div class="container">
		@content
	</div>

</body>

</html>