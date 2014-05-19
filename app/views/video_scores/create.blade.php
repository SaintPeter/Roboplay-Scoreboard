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
.scrolling_filelist {
	width: 100%;
	height: 390px;
	margin: 0px;
	overflow: auto;
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
<div style="width:950px" class="center-block clearfix">
	<div class="pull-left" style="width:640px; margin: 10px;">
		<span class="pull-right">{{ $video->vid_division->name }}</span>
		<h4>{{ $video->name }} </h4>
		<iframe  style="border: 1px solid black" id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}?rel=0" frameborder="0"></iframe>
	</div>

	<div class="pull-left" style="width: 250px; margin: 10px 20px;">
		@include('partials.filelist', [ 'video' => $video, 'show_type' => false, 'show_delete' => false ])
	</div>
</div>

{{ Form::open(['route' => [ 'video.judge.store', $video->id ] ]) }}
<table class="score_table">
	@foreach($types as $type)
		<tr class="title_row">
			<td class="name_col">{{ $type->display_name }}</td>
			<td class="score_col">1</td>
			<td class="score_col">2</td>
			<td class="score_col">3</td>
			<td class="score_col">4</td>
		</tr>
		@foreach($type->rubric as $rubric)
			<tr class="score_row">
				<td class="cat_col">
						<a href="#" rubric_id="{{ $rubric->id }}" class="rubric_switcher">
							<span id="icon_{{ $rubric->id }}" class="glyphicon glyphicon-chevron-right"></span>
							{{ $rubric->element_name }}
						</a>
				</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '1', true) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '2', false) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '3', false) }}</td>
				<td class="cb_col">{{ Form::radio('scores[' . $type->id .  '][' . $rubric->element . ']', '4', false) }}</td>
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
	<tr>
		<td colspan="5" style="text-align: center">
			{{ Form::submit('Save', ['class' => 'btn btn-success']) }}
		</td>
	</tr>
</table>
{{ Form::close() }}
<br />
<br />
<br />
@stop