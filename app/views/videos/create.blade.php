@extends('layouts.scaffold')

@section('main')

<h1>Create Video</h1>

{{ Form::open(array('route' => 'videos.store')) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('yt_code', 'Yt_code:') }}
            {{ Form::text('yt_code') }}
        </li>

        <li>
            {{ Form::label('students', 'Students:') }}
            {{ Form::textarea('students') }}
        </li>

        <li>
            {{ Form::label('has_custom', 'Has_custom:') }}
            {{ Form::checkbox('has_custom') }}
        </li>

        <li>
            {{ Form::label('school_name', 'School_name:') }}
            {{ Form::text('school_name') }}
        </li>

        <li>
            {{ Form::label('vid_division_id', 'Vid_division_id:') }}
            {{ Form::input('number', 'vid_division_id') }}
        </li>

		<li>
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


