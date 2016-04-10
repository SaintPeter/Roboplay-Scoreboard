@extends('layouts.scaffold')

<?php
	$team_count = 0;
	$team_actual = 0;
	$video_count = 0;
	$video_actual = 0;
	$math_count = 0;
	$students_count = 0;
?>

@section('script')

@stop

@section('main')
<div class="pull-right">
	<ul class="nav nav-pills">
		@for($year_counter = 2014; $year_counter <= Carbon\Carbon::now()->year; $year_counter++)
			<li @if($year_counter == $year) class="active" @endif>{{ link_to_route('invoice_review', $year_counter, [ $year_counter ]  ) }}</li>
		@endfor
	</ul>
</div>
<table class="table">
<thead>
	<tr>
		<th>Teacher</th>
		<th>School</th>
		<th>Teams</th>
		<th>Videos</th>
		<th>Students</th>
	</tr>
</thead>
<tbody>
@if(!empty($invoices))
	@foreach($invoices as $invoice)
	<tr>
		<td>
			{{ link_to('mailto:' . $invoice->wp_user->user_email, $invoice->wp_user->getName()) }}
		</td>
		<td>
			{{ ($invoice->school) ? $invoice->school->name : "(Not Set)" }}
		</td>
		<td>
			{{ $invoice->teams->count() }} / {{ $invoice->team_count }}
			<?php $team_count += $invoice->team_count ?>
		</td>
		<td>
			{{ $invoice->videos->count() }} / {{ $invoice->video_count }}
			<?php $video_count += $invoice->videos->count() ?>
		</td>
		<td>
			{{ $invoice->videos->reduce($student_count, 0) }}
			{{ $invoice->teams->reduce($student_count, 0) }}
			<?php
			    $students_count += $invoice->videos->reduce($student_count, 0);
			    $students_count += $invoice->teams->reduce($student_count, 0);
			?>
		</td>

	</tr>
	@if($invoice->videos->count() > 0)
	<tr>
	    <td colspan="5">
	    <table class="table">
	        <tbody>
		@foreach($invoice->videos as $video)
		<tr>
			<td>&nbsp;</td>
			<td>{{ link_to_route('videos.show', $video->name, [ $video->id ], [ 'target' => '_blank' ]) }}</td>
			<td>{{ $video->vid_division->name  }}</td>
			<td>{{ $video->has_custom==1 ? '<span class="btn btn-warning btn-xs">Custom</span>' : '&nbsp;' }}</td>
			<td>{{ $video->has_vid==1 ? '<span class="btn btn-success btn-xs">Video File</span>' : '<span class="btn btn-danger btn-xs">No Video</span>' }}</td>
			<td>{{ $video->has_code==1 ? '<span class="btn btn-info btn-xs">Code</span>' : '<span class="btn btn-danger btn-xs">No Code</span>' }} </td>
			<td>{{ $video->students->count() }}</td>

		</tr>
		@endforeach
		</tbody>
		</table>
		</td>
	</tr>
	@endif
		<?php
			$team_actual += $invoice->team_count;
			$video_actual += $invoice->video_count;
		?>
	@endforeach
	<tr>
		<td colspan="2" class="text-right">Totals</td>
		<td>{{ $team_actual }} / {{ $team_count }}</td>
		<td>{{ $video_actual }} / {{ $video_count }}</td>
		<td>{{ $students_count }}</td>
	</tr>
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@stop