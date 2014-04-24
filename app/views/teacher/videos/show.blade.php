@extends('layouts.scaffold')

@section('main')

<h1>Show Video</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('teacher.videos.index', 'Return to all videos') }}</p>

<p><strong>School:</strong> {{{ $video->school_name }}} </p>
<p><strong>Division:</strong> {{{ $video->vid_division_id }}}</p>

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
{{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id))) }}
	{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
{{ Form::close() }}

@stop
