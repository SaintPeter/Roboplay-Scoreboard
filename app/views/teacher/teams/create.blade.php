@extends('layouts.scaffold')

@section('main')

<h1>Create Team - {{ $school_name }}</h1>
{{ Breadcrumbs::render() }}
{{ Form::open(array('route' => 'teacher.teams.store', 'role'=>"form", 'class' => 'col-md-6')) }}
        <div class="form-group">
            {{ Form::label('name', 'Team Name:') }}
            {{ Form::text('name','', array('class'=>'form-control col-md-4')) }}
        </div>

        <div class="form-group">
            {{ Form::label('division_id', 'Division:') }}
            {{ Form::select('division_id', $divisions, '', array('class'=>'form-control col-md-4')) }}
        </div>

 		{{ Form::submit('Submit', array('class' => 'btn btn-info ')) }}

{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


