
<div style="margin: 10px 0px;" class="pull-right">
	<ul class="nav nav-pills">
		<?php $selected_year = Session::get('selected_year', false); ?>
		@for($year = 2014; $year <= Carbon\Carbon::now()->year; $year++)
			<li @if($year == $selected_year) class="active" @endif>{{ link_to_route(Route::currentRouteName(), $year, [ 'selected_year' => $year ]) }}</li>
		@endfor
		@if($selected_year)
			<li>
				<a href="{{ route(Route::currentRouteName(), [ 'selected_year' => 'clear' ]) }}">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
			</li>
		@endif
	</ul>
</div>