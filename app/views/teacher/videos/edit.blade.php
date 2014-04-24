@extends('layouts.scaffold')

@section('main')

<h1>Edit Video</h1>
{{ Form::model($video, array('method' => 'PATCH', 'route' => array('teacher.videos.update', $video->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('yt_code', 'YouTube URL or Code:') }}
            {{ Form::text('yt_code') }}
            <p>Accepted Formats:</p>
            <ul>
            	<li>http://www.youtube.com/watch?v=-wtIMTCHWuI</li>
				<li>http://www.youtube.com/v/-wtIMTCHWuI</li>
				<li>http://youtu.be/-wtIMTCHWuI</li>
				<li>-wtIMTCHWuI (Just the code)</li>
            </ul>
        </li>

        <li>
            {{ Form::label('students', 'Students:') }}
            {{ Form::textarea('students') }}
        </li>

        <li>
            {{ Form::label('has_custom', 'Has a Custom Part:') }}
            {{ Form::checkbox('has_custom') }}
        </li>

        <li>
			{{ Form::label('vid_division_id', 'Video Division:') }}
            {{ Form::select('vid_division_id', $vid_divisions, $video->division_id, array('class'=>'form-control col-md-4')) }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('teacher.videos.show', 'Cancel', $video->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
