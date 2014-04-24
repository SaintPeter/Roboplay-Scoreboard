@extends('layouts.scaffold')

@section('main')

<h1>Edit Team</h1>
{{ Breadcrumbs::render() }}
{{ Form::model($team, array('method' => 'PATCH', 'route' => array('teams.update', $team->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('division_id', 'Division:') }}
            {{ Form::select('division_id', $divisions, $team->division_id) }}
        </li>

        <li>
            {{ Form::label('school_name', 'School Name') }}
            {{ Form::text('school_name') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('teams.show', 'Cancel', $team->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
