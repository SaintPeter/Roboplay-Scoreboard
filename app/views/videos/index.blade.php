@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
@stop

@section('script')
	var delete_id = 0;
	$(function() {
		$(".delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete video": function() {
					$( this ).dialog( "close" );
					$('#delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$("#video_table").filterTable();
	});
@stop

@section('main')
<h1>All Videos</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('videos.create', 'Add Video', [],  [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered" id="video_table">
	<thead>
		<tr>
			<th>Name</th>
			<th>YT Code</th>
			<th>Students</th>
			<th>Custom</th>
			<th>Uploads</th>
			<th>County/District/School</th>
			<th>Challenge/Division</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
	@if($videos->count())
		@foreach ($videos as $video)
			<tr>
				<td>{{{ $video->name }}}</td>
				<td>{{{ $video->yt_code }}}</td>
				<td>{{ nl2br($video->students) }}</td>
				<td>{{{ $video->has_custom==1 ? 'Yes' : 'No' }}}</td>
				<td class="{{ $video->has_vid==1 ? 'confirmed' : 'unconfirmed' }}">
					{{ $video->has_vid==1 ? 'Video File' : 'No Video' }} <br />
					{{ $video->has_code==1 ? 'Code File' : 'No Code' }} <br />
				</td>
				<td>
					@if(isset($video->school))
						<strong>C:</strong> {{ $video->school->district->county->name }}<br />
						<strong>D:</strong> {{ $video->school->district->name }}<br />
						<strong>S:</strong> {{ $video->school->name }}
					@else
						Not Set
					@endif
				</td>
				<td>
					@if(isset($video->vid_division))
						{{ $video->vid_division->competition->name }}<br />
						{{ $video->vid_division->name }}
					@else
						No Division Set
					@endif
				</td>
                <td>
                	{{ link_to_route('videos.show', 'Show', array($video->id), array('class' => 'btn btn-default')) }}
                	&nbsp;
                	{{ link_to_route('videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
					&nbsp;
					{{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
					&nbsp;
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;')) }}
                        {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $video->id)) }}
                    {{ Form::close() }}
                </td>
			</tr>
		@endforeach
	@else
		<tr><td colspan="7">No Videos Entered</td></tr>
	@endif
	</tbody>
</table>

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
@stop
