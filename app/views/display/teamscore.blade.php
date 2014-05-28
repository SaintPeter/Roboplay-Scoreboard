@extends('layouts.scaffold')

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
@stop

@section('script')
@if(Roles::isAdmin())
	$(function() {
		$(".delete_button").click(function(){
			if (!confirm("Do you want to delete")){
				return false;
			}
		});
	});
@endif
	var hidden = true;
	$(function() {
		$('#show_judge').click(function(e) {
			e.preventDefault();
			$(".judge_name").toggleClass('hidden');
			hidden = !hidden;
			if(hidden) {
				$('#show_judge').html('Show Judges');
			} else {
				$('#show_judge').html('Hide Judges');
			}
		})
	});
@stop

@section('main')
<table class="table table-striped table-bordered">
	<thead>

	</thead>
	<tbody>
		@foreach($challenge_list as $number => $challenge)
			<tr>
				<td colspan="{{ 6 + 3 }}" class="info">
					<strong>Challenge {{ $number }} - {{ $challenge['name'] }}
					<span class="pull-right">{{ $challenge['points'] }} Points Possible</span>
					</strong>
				</td>
			</tr>
				@if($challenge['has_scores'])
					<tr class="bold_row">
						<td class="text-right">Score Elements</td>
						@for($se = 1; $se <= 6; $se++)
							<td>{{ $se }}</td>
						@endfor
						<td>Total</td>
						<td>Score</td>
					</tr>
					<?php $first = true; ?>
					@foreach($challenge['runs'] as $run_number => $score_run)
					<tr>
						<td class="text-right">
							@if(Roles::isAdmin())
								<a href="{{ route('display.teamscore.delete_score', [ $team->id, $score_run['id'] ]) }}" class="delete_button btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></a>
							@endif
							Run {{ $run_number }} ({{ $score_run['run_time'] }})
							<span class="judge_name hidden"><br />{{ $score_run['judge'] }}</span>
						</td>
						@for($se = 0; $se < 6; $se++)
							<td>{{ $score_run['scores'][$se] }}</td>
						@endfor
						<td>{{ $score_run['total'] }}</td>
						@if($first)
							<td rowspan="{{ $challenge['score_count'] }}" class="text-center" style="vertical-align:middle;">
								<h3>{{ $challenge['score_max'] }}</h3>
							</td>
						@endif
						<?php $first = false; ?>
					</tr>
					@endforeach
				@else
					<tr><td colspan="{{ 6 + 3}}">No Runs</td></tr>
				@endif
			</tr>
		@endforeach
		<tr>
			<td colspan="{{ 6 + 2 }}" class="text-right success"><h3>Grand Total</h3></td>
			<td class="text-center warning"><h3>{{$grand_total }}</h3></td>
		</tr>
	</tbody>
</table>
{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}
<a href="#" id="show_judge" class="btn btn-info btn-margin">Show Judges</a>
<span class="pull-right">
	{{ link_to_route('score.score_team', 'Score Team', [ $team->division->competition->id, $team->division->id, $team->id ], [ 'class' => 'btn btn-success btn-margin']) }}
</span>

@stop