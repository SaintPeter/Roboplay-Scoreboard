@extends('layouts.scaffold')

@section('main')

<h1>Show Team</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('teams.index', 'Return to all teams') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $team->name }}}</td>
			<td>{{ nl2br($team->students) }}</td>
			<td>{{{ $team->division->longname() }}}</td>
			<td>
				@if(isset($team->school))
					<strong>C:</strong> {{ $team->school->district->county->name }}<br />
					<strong>D:</strong> {{ $team->school->district->name }}<br />
					<strong>S:</strong> {{ $team->school->name }}
				@else
					Not Set
				@endif
			</td>
    	    <td>{{ link_to_route('teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}</td>
		</tr>
	</tbody>
</table>

@stop
