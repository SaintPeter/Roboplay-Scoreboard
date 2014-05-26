@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/moment.min.js') }}
@stop

@section('script')
@if(isset($next_event) AND isset($this_event))
	var endTime = moment("{{ $next_event->start }}", "hh:mm:ss");
	var clock = setInterval(function() {
		var now =  moment();
		$("#clock").html(now.format("h:mm:ss"));
	 }, 1000);
	 var timer = setInterval(function() {
	 	var delta = moment.duration(endTime.diff(moment()));
	 	if(delta.asSeconds() < 60 && delta.asSeconds() > 29 && !$('#timer').hasClass('label-warning')) {
	 		$('#timer').removeClass('label-info');
	 		$('#timer').addClass('label-warning');
	 	}
	 	if(delta.asSeconds() < 30  && !$('#timer').hasClass('label-danger')) {
	 		$('#timer').removeClass('label-warning');
	 		$('#timer').addClass('label-danger');
	 	}
	 	if(delta.asSeconds() < 1) {
	 		location.reload(true);
	 	}
	 	$("#timer").html(delta.hours() + ':' + prefix(delta.minutes()) + ':' + prefix(delta.seconds()));
	 }, 1000);


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