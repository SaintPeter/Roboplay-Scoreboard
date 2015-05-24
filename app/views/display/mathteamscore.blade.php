@extends('layouts.scaffold')

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
	.deleted_score > td {
		text-decoration: line-through;
		color: darkgrey;
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
		@foreach($challenges as $challenge)
			<tr>
				<td colspan="3" class="info">
					<strong>Challenge {{ $challenge->order }} - {{ $challenge->display_name }}
					<span class="pull-right">{{ $challenge->points }} Points Possible</span>
					</strong>
				</td>
			</tr>
				<?php $scores = Roles::isJudge() ? $challenge->scores_with_trash : $challenge->scores; ?>
				@if(count($scores) > 0)
					<tr class="bold_row">
						<td>Run</td>
						<td>Score</td>
						<td>Total</td>
					</tr>
					<?php $first = true; ?>
					@foreach($scores as $score)
					<tr {{ $score->trashed() ? 'class="deleted_score"' : '' }}>
						<td class="text-right">
							@if(Roles::isJudge())
								@if($score->trashed())
									<a href="{{ route('display.mathteamscore.restore_score', [ $team->id, $score->id ]) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></a>
								@else
									<a href="{{ route('display.mathteamscore.delete_score', [ $team->id, $score->id ]) }}" class="delete_button btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span></a>
									<a href="{{ route('math_score.editscore', [ $score->id ]) }}" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-edit"></span></a>
								@endif
							@endif
							Run {{ $score->run }} ({{ $score->run_time }})
							<span class="judge_name hidden"><br />{{ $score->judge->display_name }}</span>
						</td>
						<td>{{ $score->score }}</td>
						@if($first)
							<td rowspan="{{ $scores->count() }}" class="text-center" style="vertical-align:middle;">
								<h3>{{ $challenge->total }}</h3>
							</td>
						@endif
						<?php $first = false; ?>
					</tr>
					@endforeach
				@else
					<tr><td colspan="3">No Runs</td></tr>
				@endif
			</tr>
		@endforeach
		<tr>
			<td colspan="2" class="text-right success"><h3>Grand Total</h3></td>
			<td class="text-center warning"><h3>{{$grand_total }}</h3></td>
		</tr>
	</tbody>
</table>
{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}
<a href="#" id="show_judge" class="btn btn-info btn-margin">Show Judges</a>
@if(Roles::isJudge())
<span class="pull-right">
	{{ link_to_route('math_score.score_team', 'Score Team', [ $team->division->competition->id, $team->division->id, $team->id ], [ 'class' => 'btn btn-success btn-margin']) }}
</span>
@endif

@stop