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

@include('students.partial.js', [ 'type' => 'teams' ])

@section('main')
{{ Form::model($team, array('method' => 'PATCH', 'route' => array('teacher.teams.update', $team->id), 'role'=>"form", 'class' => 'col-md-8')) }}
		<div class="form-group">
			{{ Form::label('name', 'Team Name:') }}
			{{ Form::text('name',$team->name, array('class'=>'form-control col-md-4')) }}
		</div>


		<div class="form-group">
			{{ Form::label('division_id', 'Division:') }}
			{{ Form::select('division_id', $division_list, null, [ 'class'=>'form-control col-md-4' ]) }}
		</div>

		<div class="form-group">
			{{ Form::label('student_form', 'Students:') }}
			<div class="form-inline" id="student_form">
				@if(Session::has('students') or !empty($students))
					{{-- For existing students during edit --}}
					@if(Session::has('students'))
						@foreach(Session::get('students') as $index => $student)
							@include('students.partial.create', compact('index', 'ethnicity_list', 'student' ))
						@endforeach
					@endif
					{{-- For new students during edit --}}
					@if(!empty($students))
						@foreach($students as $student)
							<?php $index++; ?>
							@include('students.partial.edit',   compact('index', 'ethnicity_list', 'student' ))
						@endforeach
					@endif
				@else
					<?php $index = -1; ?>
				@endif

			</div>
			<br />
			{{ Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student' ]) }}
			{{ Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'mass_upload_students', 'title' => 'Upload Students' ]) }}
			{{ Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students' ] ) }}
			<p><strong>Note:</strong>T-shirt sizes are only required if you have chosen the Complete Package </p>
		</div>

		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
		&nbsp;
		{{ link_to_route('teacher.index',   'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

@include('students.partial.dialogs')

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@stop
