@extends('layouts.scaffold')

@section('title', ' - Competition Score')

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
@stop

@section('main')
@foreach($divisions as $division)
	<div class="{{ $col_class }}">
		<table class="table table-striped table-bordered">
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