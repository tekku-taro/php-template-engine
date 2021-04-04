<div class="globalnav">
	<nav>
		<ul>
			@foreach ($menus as $href => $menu)
			<li><a href="[[ $href ]]">[[ $menu ]]</a></li>
			@endforeach
		</ul>
	</nav>
	<?php
    echo 'echo test nav';
    foreach ($links as $link) {
        echo $link;
    }
?>

</div>