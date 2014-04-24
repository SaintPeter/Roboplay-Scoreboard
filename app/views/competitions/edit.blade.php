@extends('layouts.scaffold')

@section('script')
	 $(function() {
		$( "#event_date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
@stop

@section('main')

<h1>Edit Competition</h1>
{{ Form::model($competition, array('method' => 'PATCH', 'route' => array('competitions.update', $competition->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('description', 'Description:') }}
            {{ Form::textarea('description') }}
        </li>

        <li>
            {{ Form::label('location', 'Location:') }}
            {{ Form::text('location') }}
        </li>

        <li>
            {{ Form::label('address', 'Address:') }}
            {{ Form::textarea('address') }}
        </li>

        <li>
            {{ Form::label('event_date', 'Event_date:') }}
            {{ Form::text('event_date') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('competitions.show', 'Cancel', $competition->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
