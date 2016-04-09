@extends('layouts.scaffold')

@section('main')
@if ($errors->any())
<div>
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

{{ Form::open(array('route' => 'compyears.store', 'class' => 'col-md-6')) }}
	<div class="form-group">
		{{ Form::label('year', 'Year') }}
		{{ Form::text('year', Input::get('year',""), [ 'class'=>'form-control col-md-4' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('invoice_type', "Invoice Type") }}
	    {{ Form::select('invoice_type', [ 1 => 'C-STEM Invoice' ], Input::get('invoice_type', 0), [ 'class'=>'form-control' ]) }}
	</div>

	<div class="form-group">
	    {{ Form::label('invoice_type_id', "Invoice Type Id") }}
	    {{ Form::text('invoice_type_id', Input::get('invoice_type_id', 0), [ 'class'=>'form-control col-md-1' ]) }}
	</div>


	<div class="form-group">
		{{ Form::label('competitions', 'Competitions') }}
		{{ Form::select('competitions[]', $competition_list, null, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 10 ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('vid_competitions', 'Video Competitions') }}
		{{ Form::select('vid_competitions[]', $vid_competition_list, null, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 10 ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('math_competitions', 'Video Competitions') }}
		{{ Form::select('math_competitions[]', $math_competition_list, null, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 10 ]) }}
	</div>

	{{ Form::submit('Submit', array('class' => 'btn btn-primary btn-margin')) }}
	{{ link_to_route('compyears.index', 'Cancel', null, [ 'class'=>'btn btn-info btn-margin' ]) }}

{{ Form::close() }}



@stop