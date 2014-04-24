@extends('layouts.scaffold')

@section('main')

<h1>Show Vid_competition</h1>

<p>{{ link_to_route('vid_competitions.index', 'Return to all vid_competitions') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Event_date</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $vid_competition->name }}}</td>
					<td>{{{ $vid_competition->event_date }}}</td>
                    <td>{{ link_to_route('vid_competitions.edit', 'Edit', array($vid_competition->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('vid_competitions.destroy', $vid_competition->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
