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
$('.vid_division').on('change', vid_division_change);
$('#toggle_notes').on('click', toggle_notes);
$('#toggle_all_videos').on('click', toggle_all_videos);
$('#toggle_all_teams').on('click', toggle_all_teams);

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
        // Toggle status
        set_video_button(button, (status === 'pass' ? 'fail' : 'pass'), id);
    });
}

function toggle_notes(e) {
    if($(this).data('status') === 'show') {
        $(this).data('status', 'hide');
        $('.video_notes_section').hide();
    } else {
        $(this).data('status', 'show');
        $('.video_notes_section').show();
    }
}

function toggle_all_videos(e) {
    if($(this).data('status') === 'show') {
        $(this).data('status', 'hide');
        $('.video_section').hide();
    } else {
        $(this).data('status', 'show');
        $('.video_section').show();
    }
}

// Update button to a specific status
function set_video_button(button, status, id) {
    $(button).removeClass('btn-default')
             .removeClass('btn-warning')
             .removeClass('btn-success')
             .data('status', status);

    if(status === 'pass') {
        $(button).addClass('btn-success').html('Pass');
    } else {
        if($('#video_notes' + id).val()) {
            $(button).addClass('btn-warning').html('Has Notes');
        } else {
            $(button).addClass('btn-default').html('Unchecked');
        }
    }
}

var timeoutId;
function notes_change(e) {
    var noteField = this;
    var id = $(noteField).data('id');
    var status_button = $('#status_toggle_' + id);

    clearTimeout(timeoutId);
    timeoutId = setTimeout(function() {
        $.post('{{ route('invoice_review.save_video_notes') }}/' + id, { 'notes': $(noteField).val() }, function(data) {
            // Update video button
            set_video_button(status_button, status_button.data('status'), id);

            // Flash the text field to show it has been written.
            $(noteField).stop()
                .animate({backgroundColor: "#90EE90"}, 500)
                .animate({backgroundColor: "#FFFFFF"}, 500);
        });
    }, 1500);

}

function vid_division_change(e) {
    var dropdown = $(this);
    var id = dropdown.data('id');

    $.get('{{ route('invoice_review.save_video_div') }}/' + id + '/' + dropdown.val(), function(data) {
        $(dropdown).stop()
                .animate({backgroundColor: "#90EE90"}, 500)
                .animate({backgroundColor: "#FFFFFF"}, 500);
    });
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
&nbsp;&nbsp; <button class="btn btn-info" id="toggle_notes" data-status="show">Has Notes</button>
&nbsp;&nbsp; <button class="btn btn-success" id="toggle_all_videos" data-status="hide">Videos</button>
&nbsp;&nbsp; <button class="btn btn-info" id="toggle_all_teams" data-status="hide">Teams</button>
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
		<td class="text-center">
			{{ $invoice->teams->count() }} / {{ $invoice->team_count }}
			<?php $team_count += $invoice->team_count ?>
		</td>
		<td class="text-center">
			{{ $invoice->videos->count() }} / {{ $invoice->video_count }}
			<?php $video_count += $invoice->videos->count() ?>
		</td>
		<td class="text-center">
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

		    @if($invoice->teams->count() > 0)
		        <button class="btn btn-info btn-sm toggle_teams" data-invoice="{{ $invoice->id }}">Teams</button>
		    @else
		        <button class="btn btn-info btn-sm " disabled>Teams</button>
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
            			<td>
            			    {{ link_to_route('videos.show', $video->name, [ $video->id ], [ 'target' => '_blank' ]) }}
            			    (<a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a>)
            			</td>
            			<td>
            			    {{ Form::select('vid_division', $vid_division_list, $video->division->id, [ 'data-id' => $video->id, 'class' => 'vid_division' ])  }}
            			</td>
            			<td class="text-center">{{ $video->has_custom==1 ? '<span class="btn btn-warning btn-xs">Custom</span>' : '&nbsp;' }}</td>
            			<td class="text-center">{{ $video->has_vid==1 ? '<span class="btn btn-success btn-xs">Video File</span>' : '<span class="btn btn-danger btn-xs">No Video</span>' }}</td>
            			<td class="text-center">{{ $video->has_code==1 ? '<span class="btn btn-info btn-xs">Code</span>' : '<span class="btn btn-danger btn-xs">No Code</span>' }} </td>
            			<td class="text-center">{{ $video->students->count() }}</td>
            			<td>
            			    @if($video->audit)
            			        <button class="btn btn-success btn-sm audit_button" data-status="pass" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark unchecked">Pass</button>
            			    @elseif(strlen($video->notes) > 0)
            			        <button class="btn btn-warning btn-sm audit_button" data-status="fail" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark Pass">Has Notes</button>
            			    @else
            			        <button class="btn btn-default btn-sm audit_button" data-status="fail"  id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark Pass">Unchecked</button>
            			    @endif
            			</td>

            		</tr>
            		<tr>
            		    <td colspan="7" class="video_notes_section">
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