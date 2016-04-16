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
$('.toggle_videos').on('click', toggle_videos);
$('.audit_button').on('click', toggle_audit);
$('.video_notes').on('input', notes_change);

function toggle_videos(e) {
    var id = $(this).data('invoice');
    if($('#videos' + id).is(":hidden")) {
        $('#videos' + id).show();
    } else {
        $('#videos' + id).hide();
    }
}

function toggle_audit(e) {
    var button = this;
    var status = $(button).data('status');
    var id = $(button).data('video');

    $.get('{{ route('invoice_review.toggle_video') }}/' + id, function(data) {
        if(status !== 'pass') {
            $(button).removeClass('btn-default')
                   .removeClass('btn-warning')
                   .addClass('btn-success')
                   .data('status','pass')
                   .html('Pass');
        } else {
            $(button).removeClass('btn-success')
                   .data('status', 'fail');
            if($('#video_notes' + id).val()) {
                $(button).addClass('btn-warning')
                   .html('Notes');
            } else {
                $(button).addClass('btn-default')
                   .html('Unchecked');
            }
        }
    });
}

var timeoutId;
function notes_change(e) {
    var noteField = this;
    var id = $(noteField).data('id');

    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() {
        $.post('{{ route('invoice_review.save_video_notes') }}/' + id, { 'notes': $(noteField).val() }, function(data) {
            $(noteField).stop()
                .animate({backgroundColor: "#90EE90"}, 500)
                .animate({backgroundColor: "#FFFFFF"}, 500);
        });
    }, 1500);

}


@stop

@section('style')
    .video_section {
        display: none;
    }
    .video_notes {
        width: 100%;
    }
@stop

@section('main')
<div class="pull-right">
	<ul class="nav nav-pills">
		@for($year_counter = 2014; $year_counter <= Carbon\Carbon::now()->year; $year_counter++)
			<li @if($year_counter == $year) class="active" @endif>{{ link_to_route('invoice_review', $year_counter, [ $year_counter ]  ) }}</li>
		@endfor
	</ul>
</div>
{{ link_to_route('invoice_sync', "Sync with Wordpress", [ $year], [ 'class' => 'btn btn-info' ]  ) }}
&nbsp;&nbsp; Last Sync: {{ $last_sync->format('D, F j, g:s a') }}
<table class="table">
<thead>
	<tr>
		<th>Teacher</th>
		<th>School</th>
		<th>Teams</th>
		<th>Videos</th>
		<th>Students</th>
		<th>Actions</th>
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
			{{ $invoice->teams->reduce($student_count, 0) }} /
			{{ $invoice->videos->reduce($student_count, 0) }}
			<?php
			    $students_count += $invoice->videos->reduce($student_count, 0);
			    $students_count += $invoice->teams->reduce($student_count, 0);
			?>
		</td>
		<td>
		    @if($invoice->videos->count() > 0)
		        <button class="btn btn-success btn-sm toggle_videos" data-invoice="{{ $invoice->id }}">Videos</button>
		    @else
		        <button class="btn btn-success btn-sm " disabled>Videos</button>
		    @endif
		</td>

	</tr>
	@if($invoice->videos->count() > 0)
	<tr>
	    <td colspan="6" class="video_section" id="videos{{ $invoice->id }}">
    	    <table class="table">
    	        <thead>
    	            <th>Video</th>
    	            <th>Division</th>
    	            <th colspan="3">Content</th>
    	            <th>Students</th>
    	            <th>Status</th>
    	        </thead>
    	        <tbody>
            		@foreach($invoice->videos as $video)
            		<tr>
            			<td>{{ link_to_route('videos.show', $video->name, [ $video->id ], [ 'target' => '_blank' ]) }}</td>
            			<td class="text-nowrap">{{ $video->vid_division->name  }}</td>
            			<td>{{ $video->has_custom==1 ? '<span class="btn btn-warning btn-xs">Custom</span>' : '&nbsp;' }}</td>
            			<td>{{ $video->has_vid==1 ? '<span class="btn btn-success btn-xs">Video File</span>' : '<span class="btn btn-danger btn-xs">No Video</span>' }}</td>
            			<td>{{ $video->has_code==1 ? '<span class="btn btn-info btn-xs">Code</span>' : '<span class="btn btn-danger btn-xs">No Code</span>' }} </td>
            			<td>{{ $video->students->count() }}</td>
            			<td>
            			    @if($video->audit)
            			        <button class="btn btn-success btn-sm audit_button" data-status="pass" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}">Pass</button>
            			    @elseif(strlen($invoice->notes) > 0)
            			        <button class="btn btn-warning btn-sm audit_button" data-status="fail" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}">Notes</button>
            			    @else
            			        <button class="btn btn-default btn-sm audit_button" data-status="fail"  id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}">Unchecked</button>
            			    @endif
            			</td>

            		</tr>
            		<tr>
            		    <td colspan="7">
                            <label>Notes</label>
                            <textarea class="video_notes" id="video_notes{{ $video->id }}"data-id="{{ $video->id }}">{{ $video->notes }}</textarea>
            		    </td>
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