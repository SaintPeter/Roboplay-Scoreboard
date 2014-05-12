@extends('layouts.scaffold')

@section('style')
.holder {
	border: 1px solid gray;
	border-radius: 4px;
	padding: 0px;
	background-color: rgb(245, 245, 245);
	height: 250px;
	margin: 10px 0 10px 0;
}

.inner {
	padding: 12px;
}

.header {
	text-align: center;
	font-weight: bold;
	border-radius: 4px 4px 0px 0px;
	width: 100%;
	margin: 0px;
	padding: 6px 12px;
	color: white;
}

.general {
	background-color: #428BCA;
}

.part {
	background-color: #5BC0DE;
}

.compute {
	background-color: #5CB85C
}
.scored_container {
	width: 700px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	clear:both;
}
.scored_table {
	width: 100%;
}
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
<h1>Score Videos</h1>
{{ Breadcrumbs::render() }}
@if(count($comp_list))
	<h4>Open Video Competitions</h4>
	@foreach($comp_list as $comp => $divs)
		<p><strong>{{$comp}}:</strong> {{ join(', ', $divs) }} </p>
	@endforeach
@else
	<h4 style="color: red;">No Open Video Competitions</h4>
@endif
	<div class="col-sm-6 col-md-4">
		<div class="holder">
			<div class="header general">General Videos</div>
			<div class="inner">
				<p>General vidoes will be scored on:
					<ul>
						<li>Storyline</li>
						<li>Choreography</li>
						<li>Interesting Task</li>
					</ul>
					<br />
				All judges may judge these videos.</p>
				<div class="text-center">
					<a class="btn btn-primary" href="{{ route('video.judge.score', [ VG_GENERAL ]) }}">Score</a>
				</div>
			</div>
		</div>
	</div>

<div class="col-sm-6 col-md-4">
	<div class="holder">
		<div class="header part">Custom Part Videos</div>
		<div class="inner">
			<p>These videos contain a custom designed part and will be scored on the design and use of that part.<br /><br />
			Judges should have a background in mechanical design or robotics.</p>
			<div class="text-center">
				<a class="btn btn-info" href="{{ route('video.judge.score', [ VG_PART ]) }}">Score</a>
			</div>
		</div>
	</div>
</div>

<div class="col-sm-6 col-md-4">
	<div class="holder">
		<div class="header compute">Computational Thinking Videos</div>
		<div class="inner">
			<p>These videos will be judged primarily on the content of the source code written to produce them.<br /><br />
			   Judges should have a background in reading source code.</p>
			<div class="text-center">
				<a class="btn btn-success" href="{{ route('video.judge.score', [ VG_COMPUTE ]) }}">Score</a>
			</div>
		</div>
	</div>
</div>
<div class="text-center"><br /><strong>Note:</strong> Each video should be scored on its own merits as compared to the rubric, rather than in comparison to other videos.</div>

<div class="scored_container">
	<h3>Previously Scored Videos</h3>
	<table class="scored_table">
		<thead>
		</thead>
		<tbody>
			@if(count($videos))
				@foreach($videos as $comp => $video_list)
					<tr class="comp_row">
						<td>{{ $comp }}</td>
						@foreach($types as $type)
							<td class="type">{{ $type }}</td>
						@endforeach
					</tr>
					@foreach($video_list as $title => $scores)
						<tr class="score_row">
							<td><strong>{{ $title }}</strong></td>
							@foreach($types as $index => $type)
								<td class="score">{{ $scores[$index] }}</td>
							@endforeach
						</tr>
					@endforeach
				@endforeach
			@else
				<tr><td>No Videos Scored</td></tr>
			@endif
		</tbody>
	</table>
</div>



@stop