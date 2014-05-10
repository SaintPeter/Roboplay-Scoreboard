@extends('layouts.scaffold')

@section('main')

<h1>Video Competitions</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('vid_competitions.create', 'Add Video Competition',[], ['class' => 'btn btn-primary']) }}</p>

@if ($vid_competitions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($vid_competitions as $vid_competition)
				<tr>
					<td>{{{ $vid_competition->name }}}</td>
					<td>{{{ $vid_competition->event_start }}}</td>
					<td>{{{ $vid_competition->event_end }}}</td>
                    <td>{{ link_to_route('vid_competitions.edit', 'Edit', array($vid_competition->id), array('class' => 'btn btn-info btn-margin')) }}

                        {{ Form::open(array('method' => 'DELETE', 'route' => array('vid_competitions.destroy', $vid_competition->id), 'style' => 'display: inline-block')) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger btn-margin')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no vid_competitions
@endif

@stop
