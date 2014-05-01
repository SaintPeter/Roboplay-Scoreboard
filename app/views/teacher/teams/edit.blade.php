@extends('layouts.scaffold')

@section('main')

<h1>Edit Team</h1>
{{ Breadcrumbs::render() }}
{{ Form::model($team, array('method' => 'PATCH', 'route' => array('teacher.teams.update', $team->id), 'role'=>"form", 'class' => 'col-md-6')) }}
	    <div class="form-group">
            {{ Form::label('name', 'Team Name:') }}
            {{ Form::text('name',$team->name, array('class'=>'form-control col-md-4')) }}
        </div>

        <div class="form-group">
            {{ Form::label('students', 'Students:') }}
            {{ Form::textarea('students', $team->students , array('class'=>'form-control col-md-4')) }}
            <p>Enter one student per line.</p>
        </div>

 		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
 		&nbsp;
 		{{ link_to_route('teacher.teams.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
