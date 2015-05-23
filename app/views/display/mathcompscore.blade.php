@extends('layouts.scaffold')

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
		<h1>{{ $title }}</h1>
		{{ link_to_route('home', 'Home', null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
	</div>
@stop

@section('main')
<div class="col-md-12 col-lg-12">
	@foreach($divisions as $division)
		<table class="table table-striped table-bordered table-condensed">
			<tr class="info">
				<th colspan="3">{{ $division->name }} Division</th>
				<th colspan="{{ $division->challenges->count() }}" class="text-center">Problems</th>
				<th rowspan="2" class="text-center" style="vertical-align: middle">Total (Runs)</th>
			</tr>
			<tr class="bold_row">
				<th>#</th>
				<th>Team</th>
				<th>School</th>
				@foreach($division->challenges as $challenge)
					<th class="text-center">{{ $challenge->order }}</th>
				@endforeach

			</tr>
			@foreach($score_list[$division->id] as $team_id => $score)
				<tr>
					<td>{{ $score['place'] }}</td>
					<td>
						{{ link_to_route('display.teamscore', $division->teams->find($team_id)->name, $team_id) }}
					</td>
					<td>
						{{ $division->teams->find($team_id)->school->name }}
					</td>
					@foreach($score['scores'] as $score_item)
						<td class="text-center">{{ $score_item }}</td>
					@endforeach
					<td class="text-center">
						{{ $score['total'] }} ({{ $score['runs'] }})
					</td>
				</tr>
			@endforeach
		</table>
	@endforeach
</div>

@stop