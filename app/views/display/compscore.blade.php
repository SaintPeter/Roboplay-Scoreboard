@extends('layouts.scaffold')

@section('title', ' - Competition Score')

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
@stop

@section('main')
<div class="clear">
	<h1>{{ $comp->name }} Scores</h1>
</div>
{{ Breadcrumbs::render() }}
@foreach($divisions as $division)
	<div class="{{ $col_class }}">
		<table class="table table-striped table-bordered">
			<thead>
				<tr class="info">
					<th colspan="2">{{ $division->name }} Division</th>
				</tr>
				<tr class="bold_row">
					<th>Team</th>
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
							{{ $score }}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endforeach

@stop