@extends('layouts.scaffold')

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

@section('script')
	var savedIndex = 0;

	function setup_delete_buttons() {
		// Setup Delete Student buttons
		$('.remove_student').off('click').click(function() {
			var index = parseInt($(this).attr('index'),10);
			$('#student_' + index).remove();
			$('#student_' + index).remove();
		});
	}


	$(function() {
		setup_delete_buttons();

		// Setup Add Student button
		$('#add_student').click(function() {
			$.get( '/scoreboard/ajax/blank_student/' + $(this).attr('index'), function(data) {
				$('#student_form').append(data);
				setup_delete_buttons();
			});
			// Update indexes on add buttons
			$(this).attr('index', parseInt($(this).attr('index'),10) + 1);
			$('#add_students').attr('index', $(this).attr('index'));
			$('#choose_students').attr('index', $(this).attr('index'));
		});

		// Choose Students Dialog
		$('#choose_students_dialog').dialog({
			autoOpen: false,
			width: 500,
			buttons: {
				"Add Students": function() {
					var data = $( "#student_list" ).serialize();
					$.post( '/scoreboard/ajax/load_students/' + savedIndex, data, function(returnData) {
						$('#student_form').append(returnData);
					});
					// Increment the index for the number of students added on
					savedIndex += data.length + 1;
					$('#student_add').attr('index', savedIndex);
					$('#students_add').attr('index', savedIndex);
					$('#choose_students').attr('index', savedIndex);
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$('#choose_students').click( function() {
			$.get('/scoreboard/ajax/student_list/teams', function(data) {
				$('#choose_students_dialog').html(data);
			});
			savedIndex = parseInt($(this).attr('index'),10);
			$('#choose_students_dialog').dialog('open');
		});
	});
@stop

@section('main')
{{ Form::open(array('route' => 'teacher.teams.store', 'role'=>"form", 'class' => 'col-md-8')) }}
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
            {{ Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student', 'index' => $index + 1 ]) }}
            {{ Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'add_students', 'title' => 'Upload Students', 'index' => $index + 1]) }}
            {{ Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students', 'index' => $index + 1]) }}
        </div>

 		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
 		&nbsp;
 		{{ link_to_route('teacher.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

<div id="choose_students_dialog" title="Choose Students">

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


