@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('math_teams.index', 'Return to all Math Teams', null, [ 'class' => 'btn btn-primary' ]) }}</p>

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
			<td>{{{ $math_team->name }}}</td>
			<td>{{ join('<br />', $math_team->student_list()) }}</td>
			<td>{{{ $math_team->division->longname() }}}</td>
			<td>
				@if(isset($math_team->school))
					<strong>C:</strong> {{ $math_team->school->district->county->name }}<br />
					<strong>D:</strong> {{ $math_team->school->district->name }}<br />
					<strong>S:</strong> {{ $math_team->school->name }}
				@else
					Not Set
				@endif
			</td>
    	    <td>{{ link_to_route('math_teams.edit', 'Edit', array($math_team->id), array('class' => 'btn btn-info')) }}</td>
		</tr>
	</tbody>
</table>

@stop
