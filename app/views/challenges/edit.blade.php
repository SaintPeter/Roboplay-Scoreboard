@extends('layouts.scaffold')

@section('main')
{{ Form::model($challenge, array('method' => 'PATCH', 'route' => array('challenges.update', $challenge->id), 'role'=>"form", 'class' => 'col-md-6')) }}
		<div class="form-group">
		    {{ Form::label('internal_name', 'Internal Name') }}
		    {{ Form::text('internal_name', null, [ 'class'=>'form-control col-md-4' ]) }}
		</div>

		<div class="form-group">
		    {{ Form::label('display_name', 'Display Name') }}
		    {{ Form::text('display_name', null, [ 'class'=>'form-control col-md-4' ]) }}
		</div>

		<div class="form-group">
		    {{ Form::label('rules', 'Rules') }}
		    {{ Form::textarea('rules', null, [ 'class'=>'form-control col-md-4' ]) }}
		</div>

		<div class="form-group">
			{{ Form::label('points', 'Points') }}
			{{ Form::text('points', null, [ 'class'=>'form-control col-md-2' ]) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary btn-margin')) }}
			{{ link_to_route('challenges.index', 'Cancel', [], ['class' => 'btn btn-info btn-margin']) }}
		</div>
{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@stop
