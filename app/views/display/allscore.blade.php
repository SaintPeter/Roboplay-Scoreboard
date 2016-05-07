@extends('layouts.scaffold', [ 'fluid' => true ])

@section('head')
	<META HTTP-EQUIV="refresh" CONTENT="120">
	{{ HTML::script('js/moment.min.js') }}
	{{ HTML::style('//cdn.jsdelivr.net/jquery.slick/1.5.0/slick.css') }}
	{{ HTML::script('//cdn.jsdelivr.net/jquery.slick/1.5.0/slick.min.js') }}
@stop

@section('script')

@if(isset($next_event) AND isset($this_event) AND 0)
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
	$(function(){
		$('#slick_container').slick({
			slidesToShow: {{ $settings['columns'] }},
			autoplay: true,
			autoplaySpeed:  {{ $settings['delay'] }},
			speed: 700,
			pauseOnHover: false,
			prevArrow: '',
			nextArrow: ''
		});

		$("#show_settings").click(function(e) {
			e.preventDefault();
			$("#dialog-settings").dialog('open');
		});

		$('#toggle_pause').on('click', function(e) {
			e.preventDefault();
			var slick = $('#slick_container').slick('getSlick');
			$(this).toggleClass('active');
			if(slick.paused) {
			    slick.slickPlay();
			    $(this).children('i').removeClass('fa-play').addClass('fa-pause');
			} else {
			    $(this).children('i').removeClass('fa-pause').addClass('fa-play');
			    slick.slickPause();
			}
		});

		$( "#dialog-settings" ).dialog({
			resizable: false,
			autoOpen: false,
			width:320,
			buttons: {
				"Apply Settings": function() {
					$( this ).dialog( "close" );
					$('#settings_form').submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
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
	#slick_container {
		font-size: {{ $settings['font-size'] }};
	}
@stop

<?php View::share( [ 'skip_title' => true, 'skip_breadcrumbs' => true ] ); ?>
@section('before_header')
	<div class="clearfix header_container">
		@if(isset($next_event) AND isset($this_event) AND 0)
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
				</div>
			@endif
		@endif
		<h1>{{ $title }}</h1>
		{{ link_to_route('home', 'Home', null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
		<a href="#" id="show_settings" class="btn btn-info btn-margin"><span class="glyphicon glyphicon-cog"></span></a>
    	<a href="#" id="toggle_pause" class="btn btn-warning btn-margin"><i class="fa fa-pause"></i></a>
	</div>
@stop

@section('main')
<div id="slick_container">
	<div class="col-md-12 col-lg-12">
		<table class="table table-striped table-bordered table-condensed">
			<tr class="info">
				<td>School</td>
				<td>Team</td>
				<td>Score (Runs)</td>
			</tr>
			<?php $rowcount = 1; ?>
			@foreach($score_list as $team_id => $score)
				<?php $rowcount++; ?>
				<tr>
					<td>
						{{ $score['school'] }}
					</td>
					<td>
						{{ link_to_route('display.teamscore', $score['name'], $team_id) }}
					</td>
					<td>
						{{ $score['total'] }} ({{ $score['runs'] }})
					</td>
				</tr>
				@if($rowcount > $settings['rows'])
					</table>
				</div>
				<div class="col-md-12 col-lg-12">
				<table class="table table-striped table-bordered table-condensed">
					<tr class="info">
    					<td>School</td>
    					<td>Team</td>
    					<td>Score (Runs)</td>
    				</tr>
    				<?php $rowcount = 1; ?>
				@endif
			@endforeach
		</table>
	</div>
</div>

<div id="dialog-settings" title="Adjust Settings">
	{{ Form::open( [ 'route' => [ 'display.all_scores_settings', $compyear->id ], 'class' => 'form-horizontal', 'id' => 'settings_form', 'style' => 'margin: 5px;' ] ) }}
		<div class="form-group">
			{{ Form::label('columns', 'Columns:', [ 'class' => 'col-sm-4 control-label' ]) }}
			<div class="col-sm-7">
				{{ Form::text('columns', $settings['columns'] , [ 'class'=>'form-control' ]) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('rows', 'Rows:', [ 'class' => 'col-sm-4 control-label' ]) }}
			<div class="col-sm-7">
				{{ Form::text('rows', $settings['rows'] , [ 'class'=>'form-control' ]) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('delay', 'Delay (ms):', [ 'class' => 'col-sm-4 control-label' ]) }}
			<div class="col-sm-7">
				{{ Form::text('delay', $settings['delay'] , [ 'class'=>'form-control' ]) }}
			</div>
		</div>
		<div class="form-group">
			{{ Form::label('font-size', 'Font Size:', [ 'class' => 'col-sm-4 control-label' ]) }}
			<div class="col-sm-7">
				{{ Form::text('font-size', $settings['font-size'] , [ 'class'=>'form-control' ]) }}
			</div>
		</div>

	{{ Form::close() }}
</div>

@stop