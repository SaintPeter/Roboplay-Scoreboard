@extends('layouts.scaffold')

@section('main')
<h1>Edit Video</h1>
{{ Breadcrumbs::render() }}
{{ Form::model($video, array('method' => 'PATCH', 'route' => array('teacher.videos.update', $video->id), 'class' => 'col-md-6')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Name:') }}
	    {{ Form::text('name', $video->name, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('yt_code', 'YouTube URL or Code:') }}
	    {{ Form::text('yt_code', $video->yt_code, [ 'class'=>'form-control col-md-4' ]) }}
	    <p>Accepted Formats:</p>
	    <ul>
	    	<li>http://www.youtube.com/watch?v=-wtIMTCHWuI</li>
			<li>http://www.youtube.com/v/-wtIMTCHWuI</li>
			<li>http://youtu.be/-wtIMTCHWuI</li>
			<li>-wtIMTCHWuI (Just the code)</li>
	    </ul>
	</div>

	<div class="form-group">
	    {{ Form::label('students', 'Students:') }}
	    {{ Form::textarea('students', $video->students) }}
	    <p>One Student Per Line</p>
	</div>

	<div class="form-group">
	    {{ Form::label('has_custom', 'Has a Custom Part:') }}
	    {{ Form::select('has_custom', [ 0 => 'No', 1 => 'Yes' ], $video->has_custom) }}
	</div>

	<div class="form-group">
		{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		 		&nbsp;
		{{ link_to_route('teacher.videos.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

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
