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
		<?php if(count($invoice->videos) > 0) {
				$rowspan = ' rowspan="' . intval(count($invoice->videos) + 1)   . '" ';
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
			<td{{ $rowspan }}>{{ isset($invoice->vid_division) ? $invoice->vid_division->name : 'Not Set' }}</td>
			<td{{ $rowspan }}>{{ $invoice->video_count }}</td>
			@if(count($invoice->videos))
				<td{{ $rowspan }}>{{ count($invoice->videos) }}
				<td>Video Name</td>
				<td>School</td>
				<td>Division</td>
			</tr>
				@foreach($invoice->videos as $video)
				<tr>
					<td>{{ link_to_route('videos.show', $video->name, [ $video->id ]) }}</td>
					<td>{{ $video->school->name }}</td>
					<td>{{ $video->vid_division->name }}</td>
				</tr>
				@endforeach
			@else
				<td>0</td>
				<td colspan="3">No Videos</td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>

@stop