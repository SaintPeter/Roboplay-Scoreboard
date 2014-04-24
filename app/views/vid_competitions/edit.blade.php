@extends('layouts.scaffold')

@section('script')
	 $(function() {
		$( "#event_date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
@stop

@section('main')

<h1>Edit Vid_competition</h1>
{{ Form::model($vid_competition, array('method' => 'PATCH', 'route' => array('vid_competitions.update', $vid_competition->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('event_date', 'Event_date:') }}
            {{ Form::text('event_date') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('vid_competitions.show', 'Cancel', $vid_competition->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop
