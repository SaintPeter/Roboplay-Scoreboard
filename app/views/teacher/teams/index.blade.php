@extends('layouts.scaffold')

@section('main')

<h1>Challenge Teams - {{ $school_name }}</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('teacher.teams.create', 'Add Team',array(), array('class' => 'btn btn-primary')) }}</p>


<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Division</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($teams->count())
			@foreach ($teams as $team)
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
			@endforeach
		@else
			<tr><td colspan="4">No Teams Created</td></tr>
		@endif

	</tbody>
</table>

@stop
