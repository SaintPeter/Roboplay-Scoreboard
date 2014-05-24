@extends('layouts.scaffold')

@section('main')
@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Invoice No</th>
			<th>User</th>
			<th>E-mail</th>
			<th>Invoice School</th>
			<th>Invoice Divsion</th>
			<th>Paid</th>
			<th>Made</th>
			<th colspan="3">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@foreach($invoices as $invoice)
		<?php if(count($invoice->teams) > 0) {
				$rowspan = ' rowspan="' . intval(count($invoice->teams) + 1)   . '" ';
			} else {
				$rowspan = '';
			}
		?>
		<tr>
			<td {{ $rowspan }}>{{ $invoice->invoice_no }}</td>
			<td{{ $rowspan }}>
				{{ isset($invoice->judge) ? $invoice->judge->display_name : Usermeta::getFullName($invoice->user->ID) }}
			</td>
			<td{{ $rowspan }}>
				{{ link_to('mailto:' . $invoice->user->user_email, $invoice->user->user_email) }}
			</td>
			<td{{ $rowspan }}>{{ isset($invoice->school) ? $invoice->school->name : 'Not Found' }}</td>
			<td{{ $rowspan }}>{{ isset($invoice->challenge_division) ? $invoice->challenge_division->name : 'Not Set' }}</td>
			<td{{ $rowspan }}>{{ $invoice->team_count }}</td>
			@if(count($invoice->teams))
				<td{{ $rowspan }}>{{ count($invoice->teams) }}
				<td>Team Name</td>
				<td>School</td>
				<td>Division</td>
				<td>Upload</td>
			</tr>
				@foreach($invoice->teams as $team)
				<tr>
					<td>{{ link_to_route('teams.show', $team->name, [ $team->id ]) }}</td>
					<td>{{ $team->school->name }}</td>
					<td>{{ isset($team->division) ? $team->division->name : 'not set' }}</td>
					<td>{{ $team->student_count() }}</td>
				</tr>
				@endforeach
			@else
				<td>0</td>
				<td colspan="4">No teams</td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>

@stop