@extends('layouts.scaffold')

<?php
	$team_count = 0;
	$video_count = 0;
	$math_count = 0;
	$students_count = 0;
?>

@section('main')
<table class="table">
<thead>
	<tr>
		<th>Teacher</th>
		<th>School</th>
		<th>Division</th>
		<th>Teams</th>
		<th>Videos</th>
		<th>Math</th>
		<th>Students</th>
	</tr>
</thead>
<tbody>
@if(!empty($invoices))
	@foreach($invoices as $invoice)
	<tr>
		<td>
			{{ $invoice->user->getName() }}
		</td>
		<td>
			{{ $invoice->user->getSchool() }}
		</td>
		<td>
			{{ $invoice->getData('Divlevel','No Division') }}
		</td>
		<td>
			{{ $invoice->teams->count() }} / {{ $invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0) }}
			<?php $team_count += $invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0); ?>
		</td>
		<td>
			{{ $invoice->videos->count() }} / {{ $invoice->getData('Video', 0) }}
			<?php $video_count += $invoice->getData('Video', 0); ?>
		</td>
		<td>
			{{ $invoice->getData('PreMath', 0) + $invoice->getData('AlgMath', 0) }}
			<?php $math_count += $invoice->getData('PreMath', 0) + $invoice->getData('AlgMath', 0); ?>
		</td>
		<td>
			{{ Student::where('teacher_id', $invoice->user->ID)->count(); }}
			<?php $students_count += Student::where('teacher_id', $invoice->user->ID)->count(); ?>
		</td>

	</tr>
	@endforeach
	<tr>
		<td colspan="3" class="text-right">Totals</td>
		<td>{{ $team_count }}</td>
		<td>{{ $video_count }}</td>
		<td>{{ $math_count }}</td>
		<td>{{ $students_count }}</td>
	</tr>
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@stop