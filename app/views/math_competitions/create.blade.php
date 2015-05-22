@extends('layouts.scaffold')

@section('script')
	 $(function() {
		$( ".date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
@stop

@section('main')
@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

{{ Form::open(array('route' => 'math_competitions.store', ' class' => 'form-horizontal col-md-4')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Name:') }}
	    {{ Form::text('name', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('event_date', 'Event Date') }}
	    {{ Form::text('event_date',null, [ 'class'=>'form-control col-md-4 date' ]) }}
	</div>


	{{ Form::submit('Submit', array('class' => 'btn btn-primary btn-margin')) }}
	{{ link_to_route('math_competitions.index', 'Cancel',  null, array('class' => 'btn btn-info btn-margin')) }}

{{ Form::close() }}


@stop


