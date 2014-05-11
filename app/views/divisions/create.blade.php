@extends('layouts.scaffold')

@section('main')

<h1>Create Division</h1>

{{ Form::open(array('route' => 'divisions.store')) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('description', 'Description:') }}
            {{ Form::text('description') }}
        </li>

        <li>
            {{ Form::label('display_order', 'Display_order:') }}
            {{ Form::input('number', 'display_order') }}
        </li>

        <li>
            {{ Form::label('competition_id', 'Competition:') }}
            {{ Form::select('competition_id', $competitions) }}
        </li>

		<li>
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
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


