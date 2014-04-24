@extends('layouts.scaffold')

@section('main')

<h1>Show Team</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('teacher.teams.index', 'Return to all teams') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Division</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $team->name }}}</td>
			<td>{{{ $team->division->longname() }}}</td>
            <td>{{ link_to_route('teacher.teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}</td>
            <td>
                {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.teams.destroy', $team->id))) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                {{ Form::close() }}
            </td>
		</tr>
	</tbody>
</table>

@stop
