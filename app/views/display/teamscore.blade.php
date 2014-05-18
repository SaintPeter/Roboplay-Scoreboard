@extends('layouts.scaffold')

@section('title', 'Team Score')

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
				<td colspan="13" class="info">
					Challenge {{ $number }} - {{ $challenge['name'] }}
				</td>
			</tr>
				@if($challenge['has_scores'])
					<tr class="bold_row">
						<td class="text-right">Score Elements</td>
						<td>1</td>
						<td>2</td>
						<td>3</td>
						<td>4</td>
						<td>5</td>
						<td>6</td>
						<td>7</td>
						<td>8</td>
						<td>9</td>
						<td>10</td>
						<td>Total</td>
						<td>Score</td>
					</tr>
					@foreach($challenge['runs'] as $run_number => $score_run)
					<tr>
						<td class="text-right">Run {{ $run_number }} ({{ $score_run['run_time'] }})</td>
						@foreach($score_run['scores'] as $score)
							<td>{{ $score }}</td>
						@endforeach
						<td>{{ $score_run['total'] }}</td>
						@if($run_number == 1)
							<td rowspan="{{ $challenge['score_count'] }}" class="text-center" style="vertical-align:middle;">
								<h3>{{ $challenge['score_max'] }}</h3>
							</td>
						@endif
					</tr>
					@endforeach
				@else
					<tr><td colspan="13">No Runs</td></tr>
				@endif
			</tr>
		@endforeach
		<tr>
			<td colspan="12" class="text-right success"><h3>Grand Total</h3></td>
			<td class="text-center warning"><h3>{{$grand_total }}</h3></td>
		</tr>
	</tbody>
</table>
{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}

@stop