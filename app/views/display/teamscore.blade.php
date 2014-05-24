@extends('layouts.scaffold')

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
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
					@foreach($challenge['runs'] as $run_number => $score_run)
					<tr>
						<td class="text-right">Run {{ $run_number }} ({{ $score_run['run_time'] }})</td>
						@for($se = 1; $se <= 6; $se++)
							<td>{{ $score_run['scores'][$se] }}</td>
						@endfor
						<td>{{ $score_run['total'] }}</td>
						@if($run_number == 1)
							<td rowspan="{{ $challenge['score_count'] }}" class="text-center" style="vertical-align:middle;">
								<h3>{{ $challenge['score_max'] }}</h3>
							</td>
						@endif
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

@stop