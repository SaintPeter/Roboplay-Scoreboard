@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.jCombo.js') }}
@stop

@section('script')
$(function() {
	$( "#select_county" ).jCombo({url: "/scoreboard/ajax/c",
			initial_text: "-- Select County --",
			selected_value: "{{ Input::old('select_county', isset($video->school) ? $video->school->district->county->county_id : 0) }}"
		});
	$( "#select_district" ).jCombo({url: "/scoreboard/ajax/d/",
			parent: "#select_county",
			initial_text: "-- Select District --",
			selected_value: "{{ Input::old('select_district', isset($video->school) ? $video->school->district->district_id : 0) }}"
		});
	$( "#select_school" ).jCombo({url: "/scoreboard/ajax/s/",
			parent: "#select_district",
			initial_text: "-- Select School --",
			selected_value: "{{ Input::old('school_id', isset($video->school) ? $video->school->school_id : 0) }}"
		});
});
@stop

@section('main')

<h1>Edit Video</h1>
{{ Form::model($video, array('method' => 'PATCH', 'route' => array('videos.update', $video->id), 'role'=>"form", 'class' => 'col-md-6')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Name:') }}
	    {{ Form::text('name', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('yt_code', 'YouTube URL or Code:') }}
	    {{ Form::text('yt_code', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('vid_division_id', 'Division:') }}
		{{ Form::select('vid_division_id', $vid_divisions, null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

    <div class="form-group">
		<label for="select_county">County:</label>
		<select name="select_county" id="select_county" class="form-control col-md-4"></select>
	</div>

	<div class="form-group">
		<label for="select_district">District:</label>
		<select name="select_district" id="select_district" class="form-control col-md-4"></select>
	</div>

	<div class="form-group">
		<label for="select_school">School:</label>
		<select name="school_id" id="select_school" class="form-control col-md-4"></select>
	</div>

	<div class="form-group">
	    {{ Form::label('students', 'Students:') }}
	    {{ Form::textarea('students') }}
	    <p>One Student Per Line</p>
	</div>

	<div class="form-group">
	    {{ Form::label('has_custom', 'Has a Custom Part:') }}
	    {{ Form::select('has_custom', [ 0 => 'No', 1 => 'Yes' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('has_vid', 'Has a Video:') }}
	    {{ Form::select('has_vid', [ 0 => 'No', 1 => 'Yes' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('has_code', 'Has a Code File:') }}
	    {{ Form::select('has_code', [ 0 => 'No', 1 => 'Yes' ]) }}
	</div>

	<div class="form-group">
		{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
		 		&nbsp;
		{{ link_to_route('videos.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

	</div>
{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif
@stop
