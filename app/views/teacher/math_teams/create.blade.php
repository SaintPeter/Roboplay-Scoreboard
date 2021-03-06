@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@stop


@section('style')
/* Fix margins for nested inline forms */
.form-inline .form-group{
	margin-left: 0;
	margin-right: 0;
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
@stop

@include('students.partial.js', [ 'type' => 'maths' ])

@section('main')
{{ Form::open(array('route' => 'teacher.math_teams.store', 'role'=>"form", 'class' => 'col-md-8')) }}
		<div class="form-group">
			{{ Form::label('name', 'Team Name:') }}
			{{ Form::text('name','', array('class'=>'form-control col-md-4')) }}
		</div>

		<div class="form-group">
			{{ Form::label('division_id', 'Division:') }}
			{{ Form::select('division_id', $division_list, '', [ 'class'=>'form-control col-md-4' ]) }}
		</div>

		<div class="form-group">
			{{ Form::label('student_form', 'Students:') }}
			<div class="form-inline" id="student_form">
				@if(Session::has('students'))
					@foreach(Session::get('students') as $index => $student)
						@include('students.partial.create', compact('index', 'ethnicity_list', 'student' ))
					@endforeach
				@else
					<?php $index = -1; ?>
				@endif
			</div>
			<br />
			{{ Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student' ]) }}
			{{ Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'mass_upload_students', 'title' => 'Upload Students' ]) }}
			{{ Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students']) }}
		</div>

		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
		&nbsp;
		{{ link_to_route('teacher.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

<div id="choose_students_dialog" title="Choose Students">

</div>

<div id="mass_upload_students_dialog" title="Choose Upload File">
	Download this <a href="/scoreboard/docs/roboplay_scoreboard_student_upload_template.xls">Excel Template</a> and follow the instructions inside to generate a csv file for upload.
  {{ Form::open([ 'route' => 'ajax.import_students_csv', 'files' => true, 'id' => 'upload_form' ] ) }}
	  {{ Form::label('csv_file', 'CSV File', array('id'=>'','class'=>'')) }}
	  {{ Form::file('csv_file') }}
  <br/>
  <!-- submit buttons -->
  {{ Form::close() }}
</div>

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@stop


