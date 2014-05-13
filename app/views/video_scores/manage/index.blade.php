@extends('layouts.scaffold')

@section('style')
.comp_row td:first-child {
	font-size: 1.1em;
	padding: 3px;
	text-align: left !important;
}
.comp_row td {
	background-color: #428BCA;
	color: white;
	text-align: center;
}

.score_row td:first-child {
	padding-left: 20px;
	width: 250px;
	text-align: left !important;
}
.score_row td {
	text-align: center;
	width: 70px;
	text-align: center;
	border: 1px solid lightgray;
}

.score {

}
td.score:nth-child(odd) {
	background-color: rgb(245, 245, 245);
}
@stop

@section('main')
<h1>Manage Video Scores</h1>
{{ Breadcrumbs::render() }}
{{ Form::open([ 'route' => 'video_scores.manage.process' ]) }}
<table class="scored_table">
		<thead>
		</thead>
		<tbody>
			@if(count($videos))
				@foreach($videos as $comp => $judge_list)
					<tr class="comp_row">
						<td>{{ $comp }}</td>
						@foreach($types as $type)
							<td class="type">{{ $type }}</td>
						@endforeach
						<td>&nbsp;</td>
					</tr>
					@foreach($judge_list as $judge => $video_list)
						<tr class="judge_row">
							<td colspan="{{ count($types)+2 }}">{{$judge}}</td>
						</tr>
						@foreach($video_list as $title => $scores)
							<tr class="score_row">
								<td><strong>{{ $title }}</strong></td>
								@foreach($types as $index => $type)
									<td class="score">{{ $scores[$index] }}</td>
								@endforeach
								<td>{{ Form::checkbox('delete',1) }}</td>
							</tr>
						@endforeach
					@endforeach
				@endforeach
			@else
				<tr><td>No Videos Scored</td></tr>
			@endif
		</tbody>
	</table>
{{ Form::close() }}
@stop