@extends('layouts.scaffold')

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

<p><strong>School:</strong> {{{ $video->school->name }}} </p>

<h3>{{{ $video->name }}}</h3>
<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}" frameborder="0"></iframe>

<h3>Students:</h3>
<p>{{ nl2br($video->students) }}</p>
@if($video->has_custom)
	<p>Video Features Custom Parts</p>
@else
	<p>Video Does not have Custom Parts</p>
@endif

{{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
&nbsp;
{{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'style' => 'display: inline-block;', 'id' => 'delete_form' )) }}
	{{ Form::submit('Delete', array('class' => 'btn btn-danger', 'id' => 'delete_button')) }}
{{ Form::close() }}

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>


@stop
