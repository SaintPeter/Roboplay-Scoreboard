@extends('layouts.scaffold')

@section('main')
@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Teacher</th>
			<th>E-mail</th>
			<th>School</th>
			<th>Division</th>
			<th>Video</th>
			<th>Student</th>
		</tr>
	</thead>
	<tbody>
		@foreach($invoices as $invoice)
			@if(count($invoice->videos))
				@foreach($invoice->videos as $video)
					@foreach($video->student_list() as $student)
					<tr>
						<td>
							{{ isset($invoice->judge) ? $invoice->judge->display_name : Usermeta::getFullName($invoice->user->ID) }}
						</td>
						<td>
							{{ link_to('mailto:' . $invoice->user->user_email, $invoice->user->user_email) }}
						</td>
						<td>{{ isset($invoice->school) ? $invoice->school->name : 'Not Found' }}</td>
						<td>{{ isset($invoice->vid_division) ? $invoice->vid_division->name : 'Not Set' }}</td>
						<td>{{ link_to_route('videos.show', $video->name, [ $video->id ]) }}</td>
						<td>{{ $student }}</td>
					</tr>
					@endforeach
				@endforeach
			@endif
		@endforeach
	</tbody>
</table>

@stop