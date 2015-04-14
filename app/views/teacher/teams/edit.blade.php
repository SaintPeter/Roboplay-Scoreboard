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

		// Upload Students dialog
		$('#add_students_dialog').dialog({
			autoOpen: false,
			width: 500,
			buttons: {
				"Upload Students": function() {
					var data = { index: savedIndex };
					$('#upload_form').ajaxSubmit({data: data,
						success: function(responseText, statusText, xhr, $form) {
							$('#student_form').append(responseText);
						}});
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$('#add_students').click( function() {
			savedIndex = parseInt($(this).attr('index'),10);
			$('#add_students_dialog').dialog('open');
		});

	});
@stop

@section('main')
{{ Form::model($team, array('method' => 'PATCH', 'route' => array('teacher.teams.update', $team->id), 'role'=>"form", 'class' => 'col-md-8')) }}
		<div class="form-group">
			{{ Form::label('name', 'Team Name:') }}
			{{ Form::text('name',$team->name, array('class'=>'form-control col-md-4')) }}
		</div>


		<div class="form-group">
			{{ Form::label('division_id', 'Division:') }}
			{{ Form::select('division_id', $division_list, '', [ 'class'=>'form-control col-md-4' ]) }}
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
			{{ Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student', 'index' => $index + 1 ]) }}
			{{ Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'add_students', 'title' => 'Upload Students', 'index' => $index + 1]) }}
			{{ Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students', 'index' => $index + 1 ] ) }}
		</div>

		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
		&nbsp;
		{{ link_to_route('teacher.index',   'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

<div id="choose_students_dialog" title="Choose Students">

</div>

<div id="add_students_dialog" title="Choose Upload File">
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
