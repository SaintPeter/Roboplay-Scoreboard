@extends('layouts.mobile')

@section('header','RoboPlay Scoreboard')

@section('navbar')
@if(Auth::guest())
<a class="ui-btn-right"
	data-icon="lock"
   data-iconpos="notext"
   href="/scoreboard/login">Login</a>
@else
<a class="ui-btn-right"
	data-icon="lock"
   data-iconpos="notext"
   data-theme="b"
   href="/scoreboard/logout">Logout</a>

@endif
@stop

@section('main')
<h2>Open Access</h2>
<ul data-role="listview" data-inset="true">
	@if(!$competitions->isEmpty())
		@foreach($competitions as $comp)
			<li>{{ link_to_route('display.compscore', $comp->name . ' Scoreboard', $comp->id, $noajax) }} </li>
		@endforeach
	@endif
</ul>

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
	<li>{{ link_to_route('teacher.teams.index', 'Manage Teams',array(),  $noajax) }}</li>
	<li>{{ link_to_route('teacher.videos.index', 'Manage Videos', [], $noajax) }}</li>
</ul>
@endif


@if(Roles::isAdmin())
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
	<li>{{ link_to('invoice_management', 'Invoice Payment Management', $noajax) }}</li>
</ul>
@endif

@stop