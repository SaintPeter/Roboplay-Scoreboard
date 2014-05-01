@extends('layouts.scaffold')

@section('main')

<h1>Create Team</h1>
{{ Breadcrumbs::render() }}
{{ Form::open(array('route' => 'teams.store')) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('division_id', 'Division:') }}
            {{ Form::select('division_id', $divisions) }}
        </li>

        <li>
            {{ Form::label('school->name', 'School Name') }}
            {{ Form::text('school->name') }}
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


