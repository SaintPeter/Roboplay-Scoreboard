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
$(document).on('ready', function() {
    $('.toggle_videos').on('click', toggle_videos);
    $('.toggle_teams').on('click', toggle_teams);
    $('.toggle_paid').on('click', toggle_paid);
    $('.audit_button').on('click', toggle_audit);
    $('.team_audit_button').on('click', team_toggle_audit);
    $('.video_notes').on('input', notes_change);
    $('.vid_division').on('change', vid_division_change);
    $('.division').on('change', division_change);
    $('#toggle_notes').on('click', toggle_notes);
    $('#toggle_all_videos').on('click', toggle_all_videos);
    $('#toggle_all_teams').on('click', toggle_all_teams);
});

function toggle_videos(e) {
    var id = $(this).data('invoice');
    if($('#videos' + id).is(":hidden")) {
        $('#videos' + id).show();
    } else {
        $('#videos' + id).hide();
    }
}

function toggle_teams(e) {
    var id = $(this).data('invoice');
    if($('#teams' + id).is(":hidden")) {
        $('#teams' + id).show();
    } else {
        $('#teams' + id).hide();
    }
}

function toggle_paid(e) {
    var id = $(this).data('invoice');
    var status = $(this).data('paid');
    $.get('{{ route('invoice_review.toggle_paid') }}/' + id, function(data) {
        if(data === "true") {
            $('#paid_' + id).html(!status);
            if(status) {
                $('#paid_' + id)
                    .html('Unpaid')
                    .data('paid', !status)
                    .addClass('btn-danger')
                    .removeClass('btn-success');
            } else {
                $('#paid_' + id)
                    .html('Paid')
                    .data('paid', !status)
                    .removeClass('btn-danger')
                    .addClass('btn-success');

            }
        }
    });

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

function team_toggle_audit(e) {
    var button = this;
    var status = $(button).data('status');
    var id = $(button).data('team');

    $.get('{{ route('invoice_review.toggle_team') }}/' + id, function(data) {
        // Toggle status
        if(status == 'pass') {
            $(button).data('status', 'fail');
            $(button).removeClass('btn-success').addClass('btn-danger').html('Unchecked');
        } else {
            $(button).data('status', 'pass');
            $(button).removeClass('btn-danger').addClass('btn-success').html('Checked');
        }
    });
}

function toggle_notes(e) {
    if($(this).data('status') === 'show') {
        $(this).data('status', 'hide').html('Show Notes');
        $('.video_notes_section').hide();
    } else {
        $(this).data('status', 'show').html('Hide Notes');
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

function toggle_all_teams(e) {
    if($(this).data('status') === 'show') {
        $(this).data('status', 'hide');
        $('.team_section').hide();
    } else {
        $(this).data('status', 'show');
        $('.team_section').show();
    }
}

// Update button to a specific status
function set_video_button(button, status, id) {
    $(button).removeClass('btn-danger')
             .removeClass('btn-warning')
             .removeClass('btn-success')
             .data('status', status);

    if(status === 'pass') {
        $(button).addClass('btn-success').html('Checked');
    } else {
        if($('#video_notes' + id).val()) {
            $(button).addClass('btn-warning').html('Has Notes');
        } else {
            $(button).addClass('btn-danger').html('Unchecked');
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

function division_change(e) {
    var dropdown = $(this);
    var id = dropdown.data('id');

    $.get('{{ route('invoice_review.save_team_div') }}/' + id + '/' + dropdown.val(), function(data) {
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
    .team_section {
        display: none;
    }
@stop

@section('main')
<div class="pull-right">
	<ul class="nav nav-pills">
	    @foreach($comp_years as $comp_year)
			<li @if($comp_year->year == $year) class="active" @endif>{{ link_to_route('invoice_review', $comp_year->year, [ $comp_year->year ]  ) }}</li>
		@endforeach
	</ul>
</div>
{{ link_to_route('invoice_sync', "Sync with Wordpress", [ $year], [ 'class' => 'btn btn-info' ]  ) }}
&nbsp;&nbsp; Last Sync: {{ $last_sync }}
&nbsp;&nbsp; <button class="btn btn-info" id="toggle_notes" data-status="show">Hide Notes</button>
&nbsp;&nbsp; <button class="btn btn-success" id="toggle_all_videos" data-status="hide">Videos</button>
&nbsp;&nbsp; <button class="btn btn-info" id="toggle_all_teams" data-status="hide">Teams</button>
<table class="table">
<thead>
	<tr>
		<th>Teacher</th>
		<th>Username</th>
		<th>School</th>
		<th>Teams</th>
		<th>Videos</th>
		<th>Students</th>
		<th>Paid</th>
		<th class="text-center">Actions</th>
	</tr>
</thead>
<tbody>
@if(!empty($invoices))
	@foreach($invoices as $invoice)
	<tr>
		<td>
			{{ link_to('mailto:' . $invoice->wp_user->user_email, $invoice->wp_user->getName(), [ 'target' => '_blank', 'title' => 'E-mail User']) }}

			<small style="white-space: nowrap">(
			    <a href="{{ route('switch_user', $invoice->wp_user->ID) }}" title="Switch to this User"><i class="fa fa-arrow-circle-right"></i></a>
			    / <a href="http://c-stem.ucdavis.edu/wp-admin/user-edit.php?user_id={{ $invoice->user_id }}" title="Edit User's Wordpress Profile" target="_blank"><i class="fa fa-pencil"></i></a>
			    )</small>
		</td>
		<td>
			{{ $invoice->wp_user->user_login }}
		</td>
		<td>
			{{ ($invoice->school) ? $invoice->school->name : "(Not Set)" }}
		</td>
		<td class="text-center">
			{{ $invoice->teams->count() }} / {{ $invoice->team_count }}
			<?php $team_count += $invoice->teams->count() ?>
		</td>
		<td class="text-center">
			{{ $invoice->videos->count() }} / {{ $invoice->video_count }}
			<?php $video_count += $invoice->videos->count() ?>
		</td>
		<td class="text-center">
			{{ $invoice->teams->reduce($student_count, 0) }} /
			{{ $invoice->videos->reduce($student_count, 0) }}
			<?php
			    // Count the students in videos and teams
			    $students_count += $invoice->videos->reduce($student_count, 0);
			    $students_count += $invoice->teams->reduce($student_count, 0);
			?>
		</td>
		<td class="text-center">
		    @if($invoice->paid)
		        <button class="toggle_paid btn btn-success" id="paid_{{ $invoice->id }}" data-paid="1" data-invoice="{{ $invoice->id }}">Paid</button>
		    @else
		        <button class="toggle_paid btn btn-danger" id="paid_{{ $invoice->id }}" data-paid="0" data-invoice="{{ $invoice->id }}">Unpaid</button>
		    @endif
		</td>
		<td class="text-center">
		    @if($invoice->teams->count() > 0)
		        <button class="btn btn-info btn-sm toggle_teams" data-invoice="{{ $invoice->id }}">Teams</button>
		    @else
		        <button class="btn btn-info btn-sm " disabled>Teams</button>
		    @endif

		    @if($invoice->videos->count() > 0)
		        <button class="btn btn-success btn-sm toggle_videos" data-invoice="{{ $invoice->id }}">Videos</button>
		    @else
		        <button class="btn btn-success btn-sm " disabled>Videos</button>
		    @endif
		</td>

	</tr>
	@if($invoice->videos->count() > 0)
	<tr>
	    <td colspan="7" class="video_section" id="videos{{ $invoice->id }}">
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
            			        <button class="btn btn-success btn-sm audit_button" data-status="pass" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark unchecked">Checked</button>
            			    @elseif(strlen($video->notes) > 0)
            			        <button class="btn btn-warning btn-sm audit_button" data-status="fail" id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark Checked">Has Notes</button>
            			    @else
            			        <button class="btn btn-danger btn-sm audit_button" data-status="fail"  id="status_toggle_{{ $video->id }}" data-video="{{ $video->id }}" title="Click to mark Checked">Unchecked</button>
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
	@if($invoice->teams->count() > 0)
	<tr>
	    <td colspan="7" class="team_section" id="teams{{ $invoice->id }}">

    	    <table class="table pull-right">
    	        <thead>
    	            <th>Team Name</th>
    	            <th>Division</th>
    	            <th class="text-center">Students</th>
    	            <th>Status</th>
    	        </thead>
    	        <tbody>
            		@foreach($invoice->teams as $team)
            		<tr>
            			<td>
            			    {{ $team->name }}
            			</td>
            			<td>
            			    {{ Form::select('division', $division_list, $team->division->id, [ 'data-id' => $team->id, 'class' => 'division' ])  }}
            			</td>
            			<td class="text-center">{{ $team->students->count() }}</td>
            			<td>
            			    @if($team->audit)
            			        <button class="btn btn-success btn-sm team_audit_button" data-status="pass" id="team_status_toggle_{{ $team->id }}" data-team="{{ $team->id }}" title="Click to mark unchecked">Checked</button>
            			    @else
            			        <button class="btn btn-danger btn-sm team_audit_button" data-status="fail"  id="team_status_toggle_{{ $team->id }}" data-team="{{ $team->id }}" title="Click to mark Checked">Unchecked</button>
            			    @endif
            			</td>
            		</tr>
            		@if(count($team->students))
                		<tr>
                		    <td></td>
                		    <td>
                		        <table class="table table-condensed">
                		            <thead>
                		                <th>Student</th>
                		                <th>Math Level</th>
                		                <th class="text-center">Math Div</th>
                		                <th class="text-center">T-Shirt</th>

                		            </thead>
                		            <tbody>
                		            @foreach($team->students as $student)
                		            <tr>
                		                <td>{{ $student->fullName() }}</td>
                		                <td>{{ $student->math_level->name }}</td>
                		                <td class="text-center">{{ $student->math_level->level }}</td>
                		                <td class="text-center">{{ $student->tshirt ? $student->tshirt : "N/A" }}</td>
                		             </tr>
                		            @endforeach
                		            </tbody>
                		        </table>

                		    </td>
                		    <td colspan=2></td>
                		</tr>
                    @endif
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
		<td colspan="3" class="text-right">Totals</td>
		<td class="text-center">{{ $team_count }} / {{ $team_actual }}</td>
		<td class="text-center">{{ $video_count }} / {{ $video_actual }}</td>
		<td class="text-center">{{ $students_count }}</td>
	</tr>
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@stop