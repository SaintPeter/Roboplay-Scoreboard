@extends('layouts.scaffold')

@section('main')

<h1>Edit Challenge</h1>
{{ Breadcrumbs::render() }}

{{ Form::model($challenge, array('method' => 'PATCH', 'route' => array('challenges.update', $challenge->id))) }}
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
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('challenges.show', 'Cancel', $challenge->id, array('class' => 'btn')) }}
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
