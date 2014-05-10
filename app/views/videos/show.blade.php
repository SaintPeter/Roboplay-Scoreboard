@extends('layouts.scaffold')

@section('style')
.video_container {
	float: left;
	width: 640;
	postition: relative;
	padding-left: 15px;
	padding-right: 15px;
}
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
	});
@stop

@section('main')

<h1>Show Video</h1>
{{ Breadcrumbs::render() }}

<table class="table table-striped table-bordered">
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
            	{{ link_to_route('videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info btn-margin')) }}
            	{{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success btn-margin')) }}
                {{ Form::open(array('method' => 'DELETE', 'route' => array('videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;')) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button btn-margin', 'delete_id' => $video->id)) }}
                {{ Form::close() }}
            </td>
		</tr>
	</tbody>
</table>

<div class="video_container">
<h3>Preview</h3>
<iframe style="border: 1px solid black" id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}" frameborder="0"></iframe>
</div>

<div class="col-md-4">
	<h3>Files</h3>
	<table class="table">
		@if(count($video->files))
			@foreach($video->files as $file)
			<tr>
				<td>{{ link_to($file->path(), $file->filename) }}</td>
				<td>{{ $file->filetype->name }}</td>
				<td><a href="{{ route('teacher.video.delete_file', [ 'video_id' => $video->id, 'file_id' => $file->id ]) }}"
					class="btn btn-danger btn-xs active"><span class="glyphicon glyphicon-remove"></span> </a></td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="3">No Files</td></tr>
		@endif
	</table>
</div>

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>


@stop
