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
			{{ link_to('mailto:' . $invoice->user->user_email, $invoice->user->getName()) }}
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
	@if($invoice->videos->count() > 0)
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
	@endif
		<?php
			$team_actual += $invoice->teams->count();
			$video_actual += $invoice->videos->count();
		?>
	@endforeach
	<tr>
		<td colspan="3" class="text-right">Totals</td>
		<td>{{ $team_actual }} / {{ $team_count }}</td>
		<td>{{ $video_actual }} / {{ $video_count }}</td>
		<td>{{ $math_count }}</td>
		<td>{{ $students_count }}</td>
	</tr>
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@stop