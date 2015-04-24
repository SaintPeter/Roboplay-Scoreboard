@extends('layouts.scaffold')


@section('main')
<table class="table">
<thead>
	<tr>
		<th>Teacher</th>
		<th>Scool</th>
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
			{{ $invoice->teams->count() }} / {{ $invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0) }}
		</td>
		<td>
			{{ $invoice->videos->count() }} / {{ $invoice->getData('Video', 0) }}
		</td>
		<td>
			{{ $invoice->getData('PreMath', 0) + $invoice->getData('AlgMath', 0) }}
		</td>
		<td>
			{{ Student::where('teacher_id', $invoice->user->ID)->count(); }}
		</td>

	</tr>
	@endforeach
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@stop