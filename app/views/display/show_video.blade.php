@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/lytebox.css') }}
	{{ HTML::script('js/lytebox.js') }}
@stop

@section('main')
<div style="width:950px" class="center-block clearfix">
	<div class="pull-left" style="width:640px; margin: 10px;">
		<span class="pull-right">{{ $video->vid_division->name }}</span>
		<h4>{{ $video->name }} </h4>
		<iframe  style="border: 1px solid black" id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/{{{ $video->yt_code }}}?rel=0" frameborder="0"></iframe>
	</div>

	<div class="pull-left" style="width: 250px; margin: 10px 20px;">
		@include('partials.filelist', [ 'video' => $video, 'show_type' => false, 'show_delete' => false ])
	</div>

</div>

{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}


@stop