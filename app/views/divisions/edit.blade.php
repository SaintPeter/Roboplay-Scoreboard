@extends('layouts.scaffold')

@section('main')

<h1>Edit Division</h1>
{{ Form::model($division, array('method' => 'PATCH', 'route' => array('divisions.update', $division->id))) }}
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
            {{ Form::label('display_order', 'Display_order:') }}
            {{ Form::input('number', 'display_order') }}
        </li>

        <li>
            {{ Form::label('competition_id', 'Competition_id:') }}
            {{ Form::select('competition_id', $competitions, $division->competition_id) }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('divisions.show', 'Cancel', $division->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
