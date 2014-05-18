@extends('layouts.scaffold')

@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Invoice No</th>
			<th>Username</th>
			<th>School_id</th>
			<th>Empty?</th>
			<th>User School ID</th>
		</tr>
	</thead>
	<tbody>
		@foreach($invoices as $invoice)
		<tr>
			<td>{{ $invoice->invoice_no }}</td>
			@if($invoice->user->usermeta->isEmpty())
				<td>{{$invoice->user->metadata['fullname'] }}</td>
			@else
				<td>{{ $invoice->user->display_name }}</td>
			@endif
			<td>{{ $invoice->school_id }}</td>
			<td>{{ $invoice->user->usermeta->isEmpty() ? "Empty" : "Not Empty" }}</td>
			@if($invoice->user->usermeta->isEmpty())
				<td>No Value</td>
			@else
				<td>{{ $invoice->user->usermeta->first()->meta_value }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>

@stop