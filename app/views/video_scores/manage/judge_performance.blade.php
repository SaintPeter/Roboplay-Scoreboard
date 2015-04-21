@extends('layouts.scaffold')

@section('style')
.judges th {
	background-color: #428BCA;
	color: white;
	padding: 2px;
	text-align: center;
}

.judges th:first-child {
	text-align: left !important;
}

tr.score_row:nth-child(odd){
	background-color: #FAFAFA;
}

tr.score_row:nth-child(odd) td:last-child {
	background-color: #CCC !important;
}
tr.score_row:nth-child(even) td:last-child {
	background-color: #EEE !important;
}

.judges td {
	width: 70px;
	text-align: center;
	border: 1px solid lightgrey;
}

.judges tbody td:first-child {
	width: 200px !important;
	text-align: left !important;
	padding-left: 12px;
}
@stop


@section('main')
@include('partials.scorenav', [ 'nav' => 'judges', 'year' => $year])

<table class="judges">
	<thead>
		<th>Judge</th>
		<th>General</th>
		<th>Part</th>
		<th>Code</th>
		<th>Total</th>
	</thead>
	<tbody>
		@foreach($judge_score_count as $judge => $counts)
		<tr class="score_row">
			<td>{{ $judge }}</td>
			<td>{{ $counts[1] }}</td>
			<td>{{ $counts[2] }}</td>
			<td>{{ $counts[3] }}</td>
			<td>{{ $counts['total'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@stop