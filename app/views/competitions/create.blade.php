@extends('layouts.scaffold')

@section('script')
	 $(function() {
		$( "#event_date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
@stop

@section('main')

<h1>Create Competition</h1>
{{ Breadcrumbs::render() }}

{{ Form::open(array('route' => 'competitions.store', 'class' => 'col-md-6')) }}
	<div class="form-group">
	    {{ Form::label('name', 'Name:') }}
	    {{ Form::text('name', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('description', 'Description:') }}
	    {{ Form::textarea('description', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('location', 'Location:') }}
	    {{ Form::text('location', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('address', 'Address:') }}
	    {{ Form::textarea('address', null, [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('event_date', 'Event Date:') }}
	    {{ Form::text('event_date', null, [ 'class'=>'form-control col-md-2 date' ]) }}
	</div>

	{{ Form::submit('Submit', array('class' => 'btn btn-primary btn-margin')) }}
	{{ link_to_route('competitions.index', 'Cancel', null, [ 'class'=>'btn btn-info btn-margin' ]) }}

{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</div>')) }}
	</ul>
</div>
@endif

@stop


