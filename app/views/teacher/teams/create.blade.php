@extends('layouts.scaffold')

@section('main')

<h1>Create Team - {{ $school->name }}</h1>
{{ Breadcrumbs::render() }}
{{ Form::open(array('route' => 'teacher.teams.store', 'role'=>"form", 'class' => 'col-md-6')) }}
        <div class="form-group">
            {{ Form::label('name', 'Team Name:') }}
            {{ Form::text('name','', array('class'=>'form-control col-md-4')) }}
        </div>

        <div class="form-group">
            {{ Form::label('students', 'Students:') }}
            {{ Form::textarea('students', '' , array('class'=>'form-control col-md-4')) }}
            <p>Enter one student per line.</p>
        </div>

 		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
 		&nbsp;
 		{{ link_to_route('teacher.teams.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

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


