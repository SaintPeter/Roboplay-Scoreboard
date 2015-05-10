
<div class="pull-right">
	<ul class="nav nav-pills">
		<?php $selected_year = isset($year) ? intval($year) : Session::get('selected_year', false); ?>
		@for($year_counter = 2014; $year_counter <= Carbon\Carbon::now()->year; $year_counter++)
			<li @if($year_counter == $selected_year) class="active" @endif>{{ link_to_route(Route::currentRouteName(), $year_counter, [ 'selected_year' => $year_counter ]) }}</li>
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