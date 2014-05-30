@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/moment.min.js') }}
@stop

@section('script')
@if(isset($next_event) AND isset($this_event))
		var serverTime = moment("{{ $start_time }}", "hh:mm:ss");
		var delta = moment().diff(serverTime);
		var endTime = moment("{{ $next_event->start }}", "hh:mm:ss");
		var sign = '';

		// Current Time Clock Function
		var clock = setInterval(function() {
			var now = moment();
			$("#clock").html(now.subtract('milliseconds', delta).format("h:mm:ss"));
		}, 1000);

		// Countdown Timer function
		var countdown_timer = setInterval(function() {
			var timer = moment.duration(endTime.diff(moment().subtract('milliseconds', delta)));
			if(timer.asSeconds() < 60 && timer.asSeconds() > 29 && !$('#timer').hasClass('label-warning')) {
				$('#timer').removeClass('label-info');
				$('#timer').addClass('label-warning');
			}
			if(timer.asSeconds() < 30 && !$('#timer').hasClass('label-danger')) {
				$('#timer').removeClass('label-warning');
				$('#timer').addClass('label-danger');
			}
		 	if(timer.asSeconds() < 0 && !$('#timer').hasClass('label-default')) {
		 		$('#timer').removeClass('label-danger');
		 		$('#timer').addClass('label-default');
		 		sign = '-';
		 	}

		 	if(timer.asSeconds() < -5) {
		 		location.reload(true);
		 	}
		 	$("#timer").html(sign + timer.hours() + ':' + prefix(Math.abs(timer.minutes())) + ':' + prefix(Math.abs(timer.seconds())));
	 	}, 1000);

		// 5 Minute Reload Timer
		setInterval( function() {
			location.reload(true);
		}, 5.2 * 60 * 1000); // 5.2 minutes * 60 seconds * 1000 Milliseconds

	// Add leading zero to single digits
	function prefix(input) {
	    return (input < 10 ? '0' : '') + input;
	}
@endif
@stop

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
	.clock_holder, .timer_holder {
		text-align: left;
	}
	.timing {
		margin-left: 35px;
		margin-right: 0px
		whitespace: nobreak;
	}
	.timing h1:first-child {
		margin-top: 0px;
	}
	.header_container {
		margin: 5px 15px;
	}
@stop

<?php View::share( [ 'skip_title' => true, 'skip_breadcrumbs' => true ] ); ?>
@section('before_header')
<div class="clearfix header_container">
	@if(isset($next_event) AND isset($this_event))
		<div class="pull-right well well-sm timing col-md-6">
			<div class="clock_holder">
				<h1>
					<span id="clock" class="label label-primary">{{ $this_event->start }}</span>
					<small>{{ $this_event->display }}</small>
				</h1>
			</div>
			<div class="timer_holder">
				<h1>
					<span id="timer" class="label label-info">0:00:00</span>
					<small>Next: {{ $next_event->display }}</small>
				</h1>
			</div>
			@if($frozen)
				@if(Roles::isAdmin())
					<a href="{{ route('display.compscore.do_not_freeze', [ $comp->id, "do_not_freeze" ]) }}">
						<span class="label label-info">Scores Frozen</span>
					</a>
				@else
					<span class="label label-info">Scores Frozen</span>
				@endif
			@endif
		</div>
	@endif
	<h1>{{ $title }}</h1>
	{{ link_to_route('home', 'Home', null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
</div>
@stop

@section('main')
@foreach($divisions as $division)

	<div class="{{ $col_class }}">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr class="info">
					<th colspan="3">{{ $division->name }} Division</th>
				</tr>
				<tr class="bold_row">
					<th>Team</th>
					<th>School</th>
					<th>Score</th>
				</tr>
			</thead>
			<tbody>
				@foreach($score_list[$division->id] as $team_id => $score)
					<tr>
						<td>
							{{ link_to_route('display.teamscore', $division->teams->find($team_id)->name, $team_id) }}
						</td>
						<td>
							{{ $division->teams->find($team_id)->school->name }}
						</td>
						<td>
							{{ $score }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endforeach

@stop