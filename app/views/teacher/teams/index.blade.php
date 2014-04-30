@extends('layouts.scaffold')

@section('style')
.Paid {
	background-color: lightgreen;
}

.Unpaid {
	background-color: pink;
}
@stop

@section('main')

<div class="pull-right" style="width: 500;">
	<table class="table table-condensed table-bordered">
		<thead>
			<tr>
				<th>County/District/School</th>
				<th>Challenge/Division</th>
				<th>Teams (Used)</th>
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
				<td>
					{{ $invoice->challenge_division->competition->name }}<br />
					{{ $invoice->challenge_division->name }}
				</td>
				<td class="text-center">{{ $invoice->team_count }} ({{ $teams->count() }})</td>
				<td class="{{ $paid }}">{{ $paid }}</td>
			</tr>
		</tbody>
	</table>
</div>

<h1>Challenge Teams - {{ $school->name }}</h1>
{{ Breadcrumbs::render() }}

@if( $teams->count() <= $invoice->team_count AND $invoice->team_count > 0)
	@if($invoice->paid == 1)
		<p>{{ link_to_route('teacher.teams.create', 'Add Team',array(), array('class' => 'btn btn-primary')) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Team Limit Reached</p>
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
