@extends('layouts.scaffold')


@section('style')
/* Fix margins for nested inline forms */
.form-inline .form-group{
	margin-left: 0;
	margin-right: 0;
}

/* Make nested form things look good */
.form-group .col-md-6 {
	padding-left: 0;
}

.vertical-container {
	display: table;
	width: 100%;
}

.vertical-container > .col-md-1 {
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	float: none;
}

.indent {
	margin-left: 20px;
}
@stop

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@stop


@include('students.partial.js', [ 'type' => 'maths', 'use_teacher_id' => true ])


@section('main')
{{ Form::open(array('route' => 'math_teams.store', 'role'=>"form", 'class' => 'col-md-8')) }}
        <div class="form-group">
            {{ Form::label('name', 'Math Team Name:') }}
            {{ Form::text('name','', array('class'=>'form-control col-md-4')) }}
        </div>

        <div class="form-group">
        	{{ Form::label('division_id', 'Division:') }}
            {{ Form::select('division_id', $division_list, '', [ 'class'=>'form-control col-md-4' ]) }}
        </div>

        <div class="form-group">
		<label for="teacher_id">Teacher:</label>
			{{ Form::select('teacher_id', $teacher_list, null, [ 'class'=>'form-control', 'id' => 'teacher_id' ]) }}
		</div>

		@include('students.partial.fields', [ 'students' => $students ])

 		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
 		&nbsp;
 		{{ link_to_route('math_teams.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

@include('students.partial.dialogs', compact('index'))

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@stop


