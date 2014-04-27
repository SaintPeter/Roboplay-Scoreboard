@extends('layouts.scaffold')

@section('style')
tr.done td{
	background-color: SkyBlue !important;
}
tr.done2 td{
	background-color: LightBlue !important;
}
@stop

@section('script')
	$(function() {
		$('.check_paid').change(function(e){
			var value = $(this).is(':checked') ? 1 : 0;
			var invoice_no = $(this).attr('invoice_no');
			$.get('ajax/set_paid/' + invoice_no + '/' + value, function(data) {
				$('#invoice_row_' + invoice_no).addClass('done');
			});
		});

		$('.select_div').change(function(e) {
			var value = $(this).val();
			var invoice_no = $(this).attr('invoice_no');
			$.get('ajax/set_div/' + invoice_no + '/' + value, function(data) {
				$('#invoice_row_' + invoice_no).addClass('done2');
			});
		});
	});
@stop

@section('main')
<h1>Invoice Payment Management</h1>
{{ Breadcrumbs::render() }}
<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Invoice #</th>
				<th>Name</th>
				<th>M</th>
				<th>County</th>
				<th>District</th>
				<th>School Name</th>
				<th>Div</th>
				<th>Set Division</th>
				<th>Total</th>
				<th>Paid</th>
			</tr>
		</thead>
		<tbody>
			@foreach($invoices as $invoice)
			<tr id="invoice_row_{{ $invoice->invoice_no }}">
				<td>{{ $invoice->invoice_no }}</td>
				<td>{{ $invoice->user->metadata['first_name'] }} {{ $invoice->user->metadata['last_name'] }}</td>
				<td><a href="mailto:{{ $invoice->user->user_email }}" class="btn btn-info btn-mini"><i class=icon-envelope>M</i> </a></td>
				<td>{{ $invoice->school->district->county->name }}</td>
				<td>{{ $invoice->school->district->name }}</td>
				<td>{{ $invoice->school->name }}</td>
				<td>{{ $invoice->division }}</td>
				<td>{{ Form::select('division_id', $divisions, $invoice->division_id ,[ 'class' => 'select_div', 'invoice_no' => $invoice->invoice_no ]) }}</td>
				<td>${{ $invoice->total }}</td>
				<td><input type="checkbox" class="check_paid" invoice_no="{{ $invoice->invoice_no }}" value="1" @if($invoice->paid)checked="checked">@endif</td>
			</tr>
			@endforeach
		</tbody>
@stop