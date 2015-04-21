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

.judge_cell {
	padding-left: 20px;
	width: 250px;
	text-align: left !important;
}

.video_cell {
	padding-left: 5px;
	width: 250px !important;
	text-align: left !important;
}

.score_row td.score {
	text-align: center;
	width: 70px;
	text-align: center;
}

.scored_table td{
	border: 1px solid lightgray;
}

tr.score_row:nth-child(odd){
	background-color: #FAFAFA;
}

.wrapper {
	width: 920px;
}

@stop

@section('main')
{{ Form::open([ 'route' => 'video_scores.manage.process' ]) }}
@include('partials.scorenav', [ 'nav' => 'by_video', 'year' => $year])
<div class="wrapper">
	<table class="scored_table">
			<thead>
			</thead>
			<tbody>
				@if(count($videos))
					@foreach($videos as $comp => $video_list)
						<tr class="comp_row">
							<td>{{ $comp }}</td>
							<td>Video</td>
							@foreach($types as $type)
								<td class="type">{{ $type }}</td>
							@endforeach
							<td>&nbsp;</td>
						</tr>
						@foreach($video_list as $video => $judge_list)
							<?php
								$first = true;
								if(count($video_list) > 1) {
										$rowspan = 'rowspan="' . count($judge_list) . '" ';
									} else {
										$rowspan = '';
									}
								ksort($video_list);
							?>

							@foreach($judge_list as $judge_name => $scores)
							<tr class="score_row">
								@if($first)
									<td {{ $rowspan }} class="judge_cell">{{$video}}</td>
									<?php $first = false; ?>
								@endif
									<td class="video_cell">{{ $judge_name }}</td>
									@foreach($types as $index => $type)
										<td class="score">{{ $scores[$index] }}</td>
									@endforeach
									<td class="score">{{ Form::checkbox('select[' . $scores['judge_id'] . '][]', $scores['video_id']) }}</td>
								</tr>
							@endforeach
						@endforeach
					@endforeach
				@else
					<tr><td>No Videos Scored</td></tr>
				@endif
			</tbody>
		</table>
		<span class="pull-right clearfix" style="margin-top: 10px">
			{{ Form::select('types', [ 0 => '-- Select Type --', 1 => 'General Scores', 2 => 'Custom Part', 3 => 'Computational Thinking', 'all' => 'All Types' ]) }}
			{{ Form::submit('Clear Selected Types', [ 'class' => 'btn btn-danger btn-margin' ]) }}

		</span>
	</div>
{{ Form::close() }}
@stop