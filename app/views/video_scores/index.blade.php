@extends('layouts.scaffold')

@section('style')
.holder {
	border: 1px solid gray;
	border-radius: 4px;
	padding: 0px;
	background-color: rgb(245, 245, 245);
	height: 250px;
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
	width: 400px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	clear:both;
}
.scored_table {
	width: 100%;
}
.title_row td {
	background-color: #428BCA;
	color: white;
	padding: 2px;
}
.score_row td:first-child {
	padding-left: 15px;
}


@stop

@section('main')
<h1>Score Videos</h1>
{{ Breadcrumbs::render() }}
	<div class=" col-md-4">
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

<div class=" col-md-4">
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

<div class=" col-md-4">
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

<div class="scored_container">
	<br />
	<h3>Previously Scored Videos</h3>
	<table class="scored_table">
		<thead>
		</thead>
		<tbody>
			@if(count($videos))
				@foreach($videos as $title => $scores)
					<tr class="title_row">
						<td><strong>{{ $title }}</strong></td>
						<td>Score</td>
					</tr>
					@foreach($scores as $score)
						<tr class="score_row">
							<td class="type">{{ $score->type->display_name }}</td>
							<td>{{$score->total }}</td>
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