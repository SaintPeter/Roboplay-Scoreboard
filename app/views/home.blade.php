@extends('layouts.mobile')

@section('header','RoboPlay Scoreboard')

@section('navbar')
@if(Auth::guest())
<a class="ui-btn-right"
	data-icon="lock"
   data-iconpos="notext"
   data-ajax="false"
   href="/scoreboard/login">Login</a>
@else
<a class="ui-btn-right"
	data-icon="lock"
   data-iconpos="notext"
   data-theme="b"
   data-ajax="false"
   href="/scoreboard/logout">Logout</a>

@endif
@stop

@section('main')
<h2>Competition Scores</h2>
<ul data-role="listview" data-inset="true">
	@if(!$competitions->isEmpty())
		@foreach($competitions as $comp)
			<li>{{ link_to_route('display.compscore', $comp->name . ' Scoreboard', $comp->id, $noajax) }} </li>
		@endforeach
	@else
		<p>No Active Competitions</p>
	@endif
</ul>

@if(!$vid_competitions->isEmpty())
<h2>Videos</h2>
<ul data-role="listview" data-inset="true">
		@foreach($vid_competitions as $comp)
			<li>{{ link_to_route('display.video_list', $comp->name . ' - Video List', $comp->id, $noajax) }} </li>
		@endforeach
</ul>
@endif

@if(Roles::isJudge())
<h2>Judge Menu</h2>
<ul data-role="listview" data-inset="true">
	<li>{{ link_to('score', 'Score Challenges', $noajax) }}</li>
	<li>{{ link_to_route('video.judge.index', 'Score Videos', [], $noajax) }}</li>
</ul>
@endif

@if(Roles::isTeacher())
<h2>Teacher Menu</h2>
<ul data-role="listview" data-inset="true">
	<li><a href="/scoreboard/docs/teacher_guide_2015.pdf" data-ajax="false">Teacher Guide</a></li>
	<li>
		<a href="{{ route('teacher.index') }}" data-ajax="false">
			Manage Teams and Videos
		</a>
	</li>
</ul>
@endif

@if(Roles::isAdmin())
<h2>Video Admin</h2>
<ul data-role="listview" data-inset="true">
	<li>{{ link_to_route('video_scores.manage.index', 'Manage Video Scores', [], $noajax) }}</li>
	<li>{{ link_to_route('video_scores.manage.summary', 'Video Score Summary', [], $noajax) }}</li>
</ul>

<h2>Admin Menu</h2>
<ul data-role="listview" data-inset="true">
	<li data-role="list-divider">Challenge Competition</li>
	<li>{{ link_to('competitions', 'Competitions', $noajax) }}</li>
	<li>{{ link_to('divisions', 'Competition Divisions', $noajax) }}</li>
	<li>{{ link_to('challenges', 'Manage Challenges', $noajax) }}</li>
	<li>{{ link_to('teams', 'Manage Teams', $noajax) }}</li>
	<li data-role="list-divider">Video Competition</li>
	<li>{{ link_to('vid_competitions', 'Video Competitions', $noajax) }}</li>
	<li>{{ link_to('vid_divisions', 'Video Competition Divisions', $noajax) }}</li>
	<li>{{ link_to('videos', 'Manage Videos', $noajax) }}</li>
	<li data-role="list-divider">Other Management</li>
	<li>{{ link_to('invoice_review', 'Invoice Review', $noajax) }}</li>
</ul>
@endif

@if(Auth::guest())
<div class="ui-body ui-body-a ui-corner-all">
	<a href="{{ route('login') }}" class="ui-btn" data-ajax="false">Login</a>
</div>
@else
<div class="ui-body ui-body-a ui-corner-all">
	<a href="{{ route('logout') }}" class="ui-btn" data-ajax="false">Logout</a>
</div>
@endif

@stop