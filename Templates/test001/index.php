@extends ( test001/layout )
@setvar( title, '書籍一覧' )
<div class="test-if">
	@if ( $sum < 10 ) <p>[[ $sum ]] は 10より小さい</p>
		@elseif ($sum> 10 && $sum < 20 ) <p>[[ $sum ]] は 10より大きく20より小さい</p>
			@else
			<p>[[ $sum ]] は 20より大きい</p>
			@endif
</div>
<div class="test-for">
	<div class="btn-group">
		@for ($i = 0; $i < 10; $i++ ) <input class="btn btn-default" type="button" value="[% $i %]">
			@endfor
	</div>
</div>

<div class="test-sanitize">
	[[ '<bold>BOLD</bold>' ]]
</div>