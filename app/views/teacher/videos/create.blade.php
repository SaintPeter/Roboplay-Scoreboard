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

.indent {
	margin-left: 20px;
}

@stop

@include('students.partial.js', [ 'type' => 'teams' ])

@section('main')
{{ Form::open(array('route' => 'teacher.videos.store','class' => 'col-md-8')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Video Title:') }}
	    {{ Form::text('name', '', [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('vid_division_id', 'Video Division:') }}
		{{ Form::select('vid_division_id', $division_list, '', [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('yt_code', 'YouTube URL or Code:') }}
	    {{ Form::text('yt_code', '', [ 'class'=>'form-control col-md-4' ]) }}
	    <p>Accepted Formats:</p>
	    <ul>
	    	<li>http://www.youtube.com/watch?v=-wtIMTCHWuI</li>
			<li>http://www.youtube.com/v/-wtIMTCHWuI</li>
			<li>http://youtu.be/-wtIMTCHWuI</li>
			<li>-wtIMTCHWuI (Just the code)</li>
	    </ul>
	</div>

	{{ Form::label('',"Content Tags") }}
	<div class="indent">
		<div class="checkbox">
			<label>
				{{ Form::hidden('has_story', 0) }}
				{{ Form::checkbox('has_story', 1) }} Storyline
			</label>
		</div>

		<div class="checkbox">
			<label>
				{{ Form::hidden('has_choreo', 0) }}
				{{ Form::checkbox('has_choreo', 1) }} Choreography
			</label>
		</div>

		<div class="checkbox">
			<label>
				{{ Form::hidden('has_task', 0) }}
				{{ Form::checkbox('has_task', 1) }} Interesting Task
			</label>
		</div>

		<div class="checkbox">
			<label>
				{{ Form::hidden('has_custom', 0) }}
				{{ Form::checkbox('has_custom',1) }} Custom Designed Part
			</label>
		</div>
	</div>
		<p><strong>Note:</strong><br/>These tags act as hints to judges about the content of your videos.  <br />
			Each video will be scored on all areas regardless of tags, except for "Custom part", <br />
			which must be flagged to be judged for that category.<br/>
			Computational Thinking will automatically be tagged when the video's code is uploaded.</p>



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

	<div class="form-group">
		{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
		 		&nbsp;
		{{ link_to_route('teacher.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

	</div>
{{ Form::close() }}


{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

<div id="choose_students_dialog" title="Choose Students">

</div>

<div id="mass_upload_students_dialog" title="Choose Upload File">
Download this <a href="/scoreboard/docs/roboplay_scoreboard_student_upload_template.xls">Excel Template</a> and follow the instructions inside to generate a csv file for upload. <br />

  {{ Form::open([ 'route' => 'ajax.import_students_csv', 'files' => true, 'id' => 'upload_form' ] ) }}
	  {{ Form::label('csv_file', 'CSV File', array('id'=>'','class'=>'')) }}
  	  {{ Form::file('csv_file') }}
	  <br/>
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


