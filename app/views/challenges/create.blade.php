@extends('layouts.scaffold')

@section('main')
{{ Form::open(array('route' => 'challenges.store')) }}
	<ul>
        <li>
            {{ Form::label('internal_name', 'Internal Name') }}
            {{ Form::text('internal_name') }}
        </li>

        <li>
            {{ Form::label('display_name', 'Display Name') }}
            {{ Form::text('display_name') }}
        </li>

        <li>
            {{ Form::label('rules', ' Rules') }}
            {{ Form::textarea('rules') }}
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


