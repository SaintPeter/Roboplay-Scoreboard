@extends('layouts.scaffold')

@section('main')
@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<div class="form-group">
		{{ implode('', $errors->all('<div class="error">:message</div>')) }}
	</div>
</div>
@endif

{{ Form::model($math_division, array('method' => 'PATCH', 'route' => array('math_divisions.update', $math_division->id), 'class' => 'col-md-6')) }}
	<div class="form-group">
		{{ Form::label('name', 'Name:') }}
		{{ Form::text('name') }}
	</div>

	<div class="form-group">
		{{ Form::label('display_order', 'Display Order:') }}
		{{ Form::input('number', 'display_order') }}
	</div>

	<div class="form-group">
		{{ Form::label('competition_id', 'Math Competition:') }}
		{{ Form::select('competition_id', $math_competitions, $math_division->competition_id) }}
	</div>

	<div class="form-group">
		{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		{{ link_to_route('math_divisions.show', 'Cancel', $math_division->id, array('class' => 'btn btn-info')) }}
	</div>
{{ Form::close() }}
@stop
