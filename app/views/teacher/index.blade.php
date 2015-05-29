@extends('layouts.scaffold')

@section('style')
.Paid, .confirmed {
	background-color: lightgreen !important;
}

.Unpaid, .unconfirmed {
	background-color: pink !important;
}

.ui-widget-overlay {
    background: url('images/ui-bg_flat_0_aaaaaa_40x100.png') repeat-x scroll 100% 100% #AAA;
    opacity: 0.3;
}

.summary_table {
	width: 500;
	background-color: white;
}

.narrow {
	width: 350px;
	white-space:nowrap;
}

.clear { clear: both; }

@stop

@section('script')
	var delete_id = 0;
	$(function() {
		$(".video_delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#video-dialog-confirm").dialog('open');
		});

		$( "#video-dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:200,
			width:500,
			modal: true,
			buttons: {
				"Delete video": function() {
					$( this ).dialog( "close" );
					$('#video_delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$(".team_delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#team-dialog-confirm").dialog('open');
		});

		$( "#team-dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:200,
			width:500,
			modal: true,
			buttons: {
				"Delete Math Team": function() {
					$( this ).dialog( "close" );
					$('#team_delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$(".math_team_delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#math_team-dialog-confirm").dialog('open');
		});

		$( "#math_team-dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:200,
			width:500,
			modal: true,
			buttons: {
				"Delete Team": function() {
					$( this ).dialog( "close" );
					$('#math_team_delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
@stop

<?php View::share('skip_title', 1) ?>

@section('before_header')
<div class="info_header">
	<div class="summary_table pull-right" >
		<table class="table table-condensed table-bordered">
			<thead>
				<tr>
					<th>County/District/School</th>
					<th>Teams (Paid For)</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>C:</strong> {{ $school->district->county->name }}<br />
						<strong>D:</strong> {{ $school->district->name }}<br />
						<strong>S:</strong> {{ $school->name }}
					</td>
					<td>
						<strong>Challenge:</strong> {{ $teams->count() }}
							({{ $invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0) }}) <br />
						<strong>Video:</strong> {{ $videos->count() }}
							({{ $invoice->getData('Video', 0) }})<br />
						<strong>Math:</strong> {{ $math_teams->count() }}
							({{ $invoice->getData('PreMath', 0) + $invoice->getData('AlgMath', 0) }})
					</td>
					<td class="{{ $paid }}">{{ $paid }}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<h1>Manage Teams and Videos</h1>
	<h2>{{ $school->name }}</h2>
	<div class="clear"></div>
</div>
@stop

@section('main')

<h3>Manage Challenge Teams</h3>
@if( $teams->count() < ($invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0)) AND ($invoice->getData('Challenge', 0) + $invoice->getData('Challenge2', 0)) > 0)
	@if($invoice->paid == 1)
		<p>{{ link_to_route('teacher.teams.create', 'Add Challenge Team',array(), array('class' => 'btn btn-primary')) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Team Limit Reached</p>
@endif

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($teams->count())
			@foreach ($teams as $team)
				<tr>
					<td>{{{ $team->name }}}</td>
					<td>{{ join('<br />', $team->student_list()) }}</td>
					<td>{{ $team->division->longname() }}</td>
	                <td>{{ link_to_route('teacher.teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}
	                	&nbsp;
	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.teams.destroy', $team->id), 'id' => 'team_delete_form_' . $team->id, 'style' => 'display: inline-block;')) }}
	                        {{ Form::submit('Delete', array('class' => 'btn btn-danger team_delete_button', 'delete_id' => $team->id)) }}
	                    {{ Form::close() }}
	                </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="4" class="text-center">No Teams Created</td></tr>
		@endif

	</tbody>
</table>

	<h3>Manage Videos</h3>
	@if( $videos->count() < $invoice->getData('Video', 0) AND $invoice->getData('Video', 0) > 0 )
		@if($invoice->paid == 1)
			<p>{{ link_to_route('teacher.videos.create', 'Add Video', [], [ 'class' => 'btn btn-primary' ]) }}</p>
		@else
			<p>Payment Not Recieved</p>
		@endif
	@else
		<p>Video Limit Reached</p>
	@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name/Division</th>
				<th>Students</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th>Files</th>
				<th>Uploads</th>
				<th class="narrow">Actions</th>
			</tr>
		</thead>

		<tbody>
			@if ($videos->count())
				@foreach ($videos as $video)
					<tr>
						<td>{{{ $video->name }}}<br />{{{ $video->vid_division->longname() }}}</td>
						<td>{{ join('<br />', $video->student_list()) }}</td>
						<td><a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a></td>
						<td>{{{ $video->has_custom==1 ? 'Yes' : 'No' }}}</td>
						<td>{{ count($video->files) }}</td>
						<td class="{{ $video->has_vid==1 ? 'confirmed' : 'unconfirmed' }}">
							{{ $video->has_vid==1 ? 'Video File' : 'No Video' }} <br />
							{{ $video->has_code==1 ? 'Code File' : 'No Code' }} <br />
						</td>
						<td>
							{{ link_to_route('teacher.videos.show', 'Preview', array($video->id), array('class' => 'btn btn-primary')) }}
							&nbsp;
		                    {{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
		                    &nbsp;
		                    {{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
		                    &nbsp;
		                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'id' => 'video_delete_form_' . $video->id, 'style' => 'display: inline-block;')) }}
		                        {{ Form::submit('Delete', array('class' => 'btn btn-danger video_delete_button', 'delete_id' => $video->id)) }}
		                    {{ Form::close() }}
	                	</td>
					</tr>
				@endforeach
			@else
				<tr><td colspan="7" class="text-center">No Videos Added</td></tr>
			@endif
		</tbody>
	</table>

<h3>Manage Math Teams</h3>
@if( $math_teams->count() < ($invoice->getData('PreMath', 0) + $invoice->getData('AlgMath', 0)))
	@if($invoice->paid == 1)
		<p>{{ link_to_route('teacher.math_teams.create', 'Add Math Team',array(), array('class' => 'btn btn-primary')) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Math Team Limit Reached</p>
@endif

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($math_teams->count())
			@foreach ($math_teams as $math_team)
				<tr>
					<td>{{{ $math_team->name }}}</td>
					<td>{{ join('<br />', $math_team->student_list()) }}</td>
					<td>{{ $math_team->division->longname() }}</td>
	                <td>{{ link_to_route('teacher.math_teams.edit', 'Edit', array($math_team->id), array('class' => 'btn btn-info')) }}
	                	&nbsp;
	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.math_teams.destroy', $math_team->id), 'id' => 'math_team_delete_form_' . $math_team->id, 'style' => 'display: inline-block;')) }}
	                        {{ Form::submit('Delete', array('class' => 'btn btn-danger math_team_delete_button', 'delete_id' => $math_team->id)) }}
	                    {{ Form::close() }}
	                </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="4" class="text-center">No Math Teams Created</td></tr>
		@endif

	</tbody>
</table>



<div id="video-dialog-confirm" style="display:none;" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="team-dialog-confirm" title="Delete Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="math_team-dialog-confirm" title="Delete Math Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop