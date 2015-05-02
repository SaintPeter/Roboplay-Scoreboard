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


@include('students.partial.js', [ 'type' => 'videos', 'use_teacher_id' => true ])

@section('main')
{{ Form::open(array('route' => 'videos.store', 'role'=>"form", 'class' => 'col-md-8' )) }}
	<div class="form-group">
		{{ Form::label('name', 'Name:') }}
		{{ Form::text('name', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('yt_code', 'YouTube URL or Code:') }}
		{{ Form::text('yt_code', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('vid_division_id', 'Division:') }}
		{{ Form::select('vid_division_id', $vid_divisions, null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
		<label for="teacher_id">Teacher:</label>
		{{ Form::select('teacher_id', $teacher_list, null, [ 'class'=>'form-control' ]) }}
	</div>

	<label>Content Tags</label>
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
	<label>Attributes</label>
	<div class="indent">
		<div class="checkbox">
			<label>
				{{ Form::hidden('has_code', 0) }}
				{{ Form::checkbox('has_code',1) }} Has Code
			</label>
		</div>

		<div class="checkbox">
			<label>
				{{ Form::hidden('has_vid', 0) }}
				{{ Form::checkbox('has_vid',1) }} Has Video
			</label>
		</div>
	</div>

	@include('students.partial.fields', [ 'students' => $students ])

	<div class="form-group">
		{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
				&nbsp;
		{{ link_to_route('videos.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

	</div>
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


