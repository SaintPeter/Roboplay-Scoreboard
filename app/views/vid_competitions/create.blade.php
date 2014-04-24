@extends('layouts.scaffold')

@section('script')
	 $(function() {
		$( "#event_date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
@stop

@section('main')

<h1>Create Vid_competition</h1>

{{ Form::open(array('route' => 'vid_competitions.store')) }}
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
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


