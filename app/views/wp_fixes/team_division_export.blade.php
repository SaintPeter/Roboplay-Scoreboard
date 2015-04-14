@extends('layouts.scaffold')

@section('main')
@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>ITeacher</th>
			<th>E-mail</th>
			<th>School</th>
			<th>Divsion</th>
			<th>Team Name</th>
			<th>School</th>

		</tr>
	</thead>
	<tbody>
		@foreach($invoices as $invoice)
		<tr>
			@if(count($invoice->teams))
				@foreach($invoice->teams as $team)
					@foreach($team->student_list() as $student)
					<tr>
						<td>
							{{ isset($invoice->judge) ? $invoice->judge->display_name : Usermeta::getFullName($invoice->user->ID) }}
						</td>
						<td>
							{{ link_to('mailto:' . $invoice->user->user_email, $invoice->user->user_email) }}
						</td>
						<td>{{ isset($invoice->school) ? $invoice->school->name : 'Not Found' }}</td>
						<td>{{ isset($invoice->challenge_division) ? $invoice->challenge_division->name : 'Not Set' }}</td>
						<td>{{ link_to_route('teams.show', $team->name, [ $team->id ]) }}</td>
						<td>{{ $student }}</td>
					</tr>
					@endforeach
				@endforeach
			@endif
		@endforeach
	</tbody>
</table>

@stop