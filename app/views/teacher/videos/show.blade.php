@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/lytebox.css') }}
	{{ HTML::script('js/lytebox.js') }}
@stop

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
	$(function() {
		$("#delete_button").click(function(e) {
			e.preventDefault();
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete Video": function() {
					$( this ).dialog( "close" );
					$("#delete_form").submit();
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
<p>{{ link_to_route('teacher.videos.index', 'Return to Videos' ,[], ['class' => 'btn btn-info']) }}</p>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Custom Parts</th>
			<th>Uploads</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $video->name }}}</td>
			<td>{{ nl2br($video->students) }}</td>
			<td>{{{ $video->has_custom==1 ? 'Has Custom Parts' : 'No Custom Parts' }}}</td>
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
            	{{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
				&nbsp;
                {{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
				&nbsp;
                {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;')) }}
                    {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $video->id)) }}
                {{ Form::close() }}
            </td>
		</tr>
	</tbody>
</table>


<div style="width:950px" class="center-block clearfix">
	<h3>Preview</h3>
	<div class="pull-left" style="width:640px; margin: 10px;">
		<h4>{{ $video->name }} </h4>
		<iframe  style="border: 1px solid black" id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}" frameborder="0"></iframe>
	</div>
	
	<div class="pull-left" style="width: 250px; margin: 10px 20px;">
		@include('partials.filelist', [ 'video' => $video, 'show_type' => true, 'show_delete' => true ])
	</div>
</div>

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>


@stop
