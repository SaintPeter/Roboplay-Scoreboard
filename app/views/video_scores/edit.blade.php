@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/lytebox.css') }}
	{{ HTML::script('js/lytebox.js') }}
@stop

@section('style')
.score_col, .cb_col, .rubric_text {
	width: 18%;
}

.name_col, .cat_col, .blank_col {
	width:28%;
}

.score_table {
	width: 800px;
	table-layout: fixed;
	margin-left: auto;
	margin-right: auto;
}

.title_row td, .score_row td {
	text-align: center;
}
.title_row td {
	background-color: #428BCA;
	color: white;
	font-weight: bold;
	padding: 4px;
}

.title_row td:first-child {
	border-top-left-radius: 4px;
	border-bottom-left-radius: 4px;
}
.title_row td:last-child {
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
}


.name_col {
	text-align: left !important;
}
.cat_col {
	text-align: left !important;
	padding-left: 15px;
}
.rubric_text {
	padding: 3px;
	border: 1px solid black;
	vertical-align: top;
}
@stop

@section('script')
	$(function() {
		$( ".rubric_switcher" ).click(function(e) {
			e.preventDefault();
			var rubric_id = $(this).attr('rubric_id');
			if( $( '.rubric_' + rubric_id ).hasClass('hidden')) {
				$( '#icon_' + rubric_id ).removeClass('glyphicon-chevron-right');
				$( '#icon_' + rubric_id ).addClass('glyphicon-chevron-down');
				$( '.rubric_' + rubric_id ).removeClass('hidden');
			} else {
				$( '#icon_' + rubric_id ).removeClass('glyphicon-chevron-down');
				$( '#icon_' + rubric_id ).addClass('glyphicon-chevron-right');
				$( '.rubric_' + rubric_id ).addClass('hidden');
			}
		});
	});

@stop

@section('main')
<div style="width:1090px" class="center-block clearfix">
	<div class="pull-left" style="width:780px; margin: 10px;">
		<span class="pull-right">{{ $video->vid_division->name }}</span>
		<h4>{{ $video->name }} </h4>
		<iframe  style="border: 1px solid black" id="ytplayer" type="text/html" width="780" height="480" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}?rel=0" frameborder="0"></iframe>
	</div>

	<div class="pull-left" style="width: 250px; margin: 10px 20px;">
		@include('partials.filelist', [ 'video' => $video, 'show_type' => false, 'show_delete' => false ])
	</div>
</div>

{{ Form::open(['route' => [ 'video.judge.update', $video->id ] ]) }}
<table class="score_table">
	@foreach($types as $type)
		<tr class="title_row">
			<td class="name_col">{{ $type->display_name }}</td>
			<td class="score_col">1</td>
			<td class="score_col">2</td>
			<td class="score_col">3</td>
			<td class="score_col">4</td>
		</tr>
		@foreach($type->Rubric as $rubric)
			<tr class="score_row">
				<td class="cat_col">
						<a href="#" rubric_id="{{ $rubric->id }}" class="rubric_switcher">
							<span id="icon_{{ $rubric->id }}" class="glyphicon glyphicon-chevron-right"></span>
							{{ $rubric->element_name }}
						</a>
				</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '1', $video_scores[$type->id][$rubric->element]==1) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '2', $video_scores[$type->id][$rubric->element]==2) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '3', $video_scores[$type->id][$rubric->element]==3) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '4', $video_scores[$type->id][$rubric->element]==4) }}
					{{ Form::hidden('scores[' . $type->id .  '][id]', $video_scores[$type->id]['id']) }}
				</td>
			</tr>
			<tr class="rubric_row hidden rubric_{{ $rubric->id }}">
				<td class="blank_col"></td>
				<td class="rubric_text">{{ $rubric->one }}</td>
				<td class="rubric_text">{{ $rubric->two }}</td>
				<td class="rubric_text">{{ $rubric->three }}</td>
				<td class="rubric_text">{{ $rubric->four }}</td>
			</tr>
			<tr class="blank_row hidden rubric_{{ $rubric->id }}">
				<td colspan="5">&nbsp</td>
			</tr>
		@endforeach
	@endforeach
	<tr class="title_row">
		<td colspan="5" style="text-align: left;">Report Problem</td>
	</tr>
	<tr>
		<td colspan="5">
			<a href="#" rubric_id="report_problem" class="rubric_switcher">
				<span id="icon_report_problem" class="glyphicon glyphicon-chevron-right"></span>
				Report Problem
			</a>
		</td>
	</tr>
	<tr class="rubric_row hidden rubric_report_problem">
		<td colspan=5">
			<div class="col-md-6">If you believe this video has violated a rule or has a significant issue, please describe the issue in detail.
				If there is specific content you believe is problematic, include a time marker (mm:ss).
				<br /><br />
				Reported videos are immediatly removed from judging.
				<br /><br />
				A report will only be submitted if you check the checkbox.
			</div>
			<div class="col-md-6">
				<label for='report_problem'>
					{{ Form::checkbox('report_problem') }}
					Report a Problem
				</label>

				{{ Form::label('comment', 'Problem Description') }}
				{{ Form::textarea('comment') }}
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="5" style="text-align: center">
			<br />
			{{ Form::submit('Update', ['class' => 'btn btn-success']) }}
		</td>
	</tr>
</table>
{{ Form::close() }}
<br />
<br />
<br />
@stop