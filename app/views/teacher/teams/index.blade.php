@extends('layouts.scaffold')

@section('style')
.Paid {
	background-color: lightgreen;
}

.Unpaid {
	background-color: lightred;
}
@stop

@section('main')

<h1>Challenge Teams - {{ $school->name }}</h1>
{{ Breadcrumbs::render() }}

<div class="pull-right" style="width: 500;">
	<table class="table table-condensed table-bordered">
		<thead>
			<tr>
				<th>County/District/School</th>
				<th>Division</th>
				<th>Challenge Teams (Used)</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<strong>C:</strong> {{ $invoice->school->district->county->name }}<br />
					<strong>D:</strong> {{ $invoice->school->district->name }}<br />
					<strong>S:</strong> {{ $invoice->school->name }}
				</td>
				<td>{{ $invoice->division->name }}</td>
				<td>{{ $invoice->team_count }} ({{ $teams->count() }})</td>
				<td class="{{ $paid }}">{{ $paid }}</td>
			</tr>
		</tbody>
	</table>
</div>

@if( $teams-> count() <= $invoice->team_count)
	<p>{{ link_to_route('teacher.teams.create', 'Add Team',array(), array('class' => 'btn btn-primary')) }}</p>
@else
<p>No More Teams May Be Created.</p>
@endif

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
