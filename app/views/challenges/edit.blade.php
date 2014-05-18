@extends('layouts.scaffold')

@section('main')
{{ Form::model($challenge, array('method' => 'PATCH', 'route' => array('challenges.update', $challenge->id))) }}
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
			{{ Form::submit('Update', array('class' => 'btn btn-primary btn-margin')) }}
			{{ link_to_route('challenges.show', 'Cancel', $challenge->id, array('class' => 'btn btn-info btn-margin')) }}
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
