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

{{ Form::model($math_competition, array('method' => 'PATCH', 'route' => array('math_competitions.update', $math_competition->id),'class' => 'form-horizontal col-md-4')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Name:') }}
	    {{ Form::text('name', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('event_date', 'Event Date') }}
	    {{ Form::text('event_date',null, [ 'class'=>'form-control col-md-4 date' ]) }}
	</div>

	{{ Form::submit('Update', array('class' => 'btn btn-primary btn-margin')) }}
	{{ link_to_route('math_competitions.index', 'Cancel',  null, array('class' => 'btn btn-info btn-margin')) }}
{{ Form::close() }}

@stop
