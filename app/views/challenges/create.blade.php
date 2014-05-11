@extends('layouts.scaffold')

@section('main')

<h1>Create Challenge</h1>
{{ Breadcrumbs::render() }}

{{ Form::open(array('route' => 'challenges.store')) }}
	<ul>
        <li>
            {{ Form::label('internal_name', 'Internal_name:') }}
            {{ Form::text('internal_name') }}
        </li>

        <li>
            {{ Form::label('display_name', 'Display_name:') }}
            {{ Form::text('display_name') }}
        </li>

        <li>
            {{ Form::label(' rules', ' Rules:') }}
            {{ Form::textarea(' rules') }}
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


