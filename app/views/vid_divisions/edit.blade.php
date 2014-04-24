@extends('layouts.scaffold')

@section('main')

<h1>Edit Video Division</h1>
{{ Form::model($vid_division, array('method' => 'PATCH', 'route' => array('vid_divisions.update', $vid_division->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('description', 'Description:') }}
            {{ Form::text('description') }}
        </li>

        <li>
            {{ Form::label('display_order', 'Display Order:') }}
            {{ Form::input('number', 'display_order') }}
        </li>

        <li>
            {{ Form::label('competition_id', 'Video Competition:') }}
            {{ Form::select('competition_id', $competitions, $vid_division->competition_id) }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('vid_divisions.show', 'Cancel', $vid_division->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
