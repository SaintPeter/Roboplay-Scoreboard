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

@section('style')
.ui-li-static.ui-collapsible > .ui-collapsible-heading {
    margin: 0;
}
.ui-li-static.ui-collapsible {
    padding: 0;
}
.ui-li-static.ui-collapsible > .ui-collapsible-heading > .ui-btn {
    border-top-width: 0;
}
.ui-li-static.ui-collapsible > .ui-collapsible-heading.ui-collapsible-heading-collapsed > .ui-btn,
.ui-li-static.ui-collapsible > .ui-collapsible-content {
    border-bottom-width: 0;
}
@stop

@section('main')
<h2>Scores and Videos</h2>
<ul data-role="listview" data-inset="true" data-shadow="false">
@if(!$compyears->isEmpty())
	@foreach($compyears as $compyear)
		<li data-role="collapsible" data-iconpos="right" data-inset="false">
			<h2>{{ $compyear->year }}</h2>
			<ul data-role="listview" data-theme="c">
				@if(!$compyear->competitions->isEmpty() )
					<li>{{ link_to_route('display.all_scores',  'Combined Scoreboard', $compyear->id, $noajax) }} </li>
					@if(Roles::isAdmin())
					    <li>{{ link_to_route('display.compyearscore.top',  'Statewide Leading Teams', $compyear->id, $noajax) }} </li>
					@endif
					@foreach($compyear->competitions as $comp)
						@if($comp->isDone())
						    <li>{{ link_to_route('display.compscore', $comp->name . ' - Scoreboard', $comp->id, $noajax) }} </li>
						@endif
						@if(Roles::isAdmin())
						    <li>{{ link_to_route('display.compscore.top', $comp->name . ' - Local Leading Teams', $comp->id, $noajax) }} </li>
						@endif
					@endforeach
				@else
					<li>No Active Competition</li>
				@endif
				@if(!$compyear->vid_competitions->isEmpty())
					@foreach($compyear->vid_competitions as $comp)
						<li>{{ link_to_route('display.video_list', $comp->name . ' Video List', $comp->id, $noajax) }} </li>
					@endforeach
				@else
					<li>No Video Competitions</li>
				@endif
			</ul>
		</li>
	@endforeach
@else
	<li>No Active Years</li>
@endif
</ul>

@if(Roles::isJudge())
<h2>Judge Menu</h2>
<ul data-role="listview" data-inset="true">
	<li>{{ link_to_route('score.choose_competition', 'Score Challenges', null, $noajax) }}</li>
	<li>{{ link_to_route('video.judge.index', 'Score Videos', null, $noajax) }}</li>
	<li><a href="{{ asset('docs/Video_Judge_Instructions_2016.pdf') }}" target="_blank"><i class="fa fa-video-camera"></i>&nbsp;Video Judging Guide</a></li>
</ul>
@endif

@if(Roles::isTeacher())
<h2>Teacher Menu</h2>
<ul data-role="listview" data-inset="true">
	<li>
		<a href="{{ route('teacher.index') }}" data-ajax="false">
			Manage Challenge and Video Teams
		</a>
	</li>
	<li><a href="http://c-stem.ucdavis.edu/wp-content/uploads/2017/01/2017_RoboPlayChallenge_TeacherInstructions.pdf" data-ajax="false" target="_blank"><i class="fa fa-users"></i>&nbsp;Teacher Guide &mdash; Challenge Teams</a></li>
	<li><a href="http://c-stem.ucdavis.edu/wp-content/uploads/2017/02/2017_RoboPlayVideo_VideoSubmissionInstructions.pdf" data-ajax="false" target="_blank"><i class="fa fa-video-camera"></i>&nbsp;Teacher Guide &mdash; Video Teams</a></li>


</ul>
	<p>Issues?<br />
		<ol>
			<li>Read the Teacher Guides above.</li>
			<li>Technical Problems?<br /> E-mail <a href="mailto:rex.schrader@gmail.com?subject=RoboPlay 2017 - Scoreboard Question&cc=sbmisewich@ucdavis.edu">rex.schrader@gmail.com</a></li>
			<li>Invoice Problems?<br /> E-mail <a href="mailto:sbmisewich@ucdavis.edu?subject=RoboPlay 2017 - Scoreboard Question">sbmisewich@ucdavis.edu</a></li>
		</ol>
		</p>
@endif

@if(Roles::isAdmin())
<h2>Video Admin</h2>
<ul data-role="listview" data-inset="true">
	<li>{{ link_to_route('video_scores.manage.index', 'Manage Video Scores', [], $noajax) }}</li>
	<li>{{ link_to_route('video_scores.manage.summary', 'Video Score Summary', [], $noajax) }}</li>
</ul>

<h2>Admin Menu</h2>
<ul data-role="listview" data-inset="true">
	<li data-role="list-divider">Competition Year</li>
	<li>{{ link_to('compyears', 'Competition Years', $noajax) }}</li>
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
	<li>{{ link_to_route('data_export', 'Data Export', null, $noajax) }}</li>
	<li>{{ link_to_route('schedule.index', 'Schedule Editor', null, $noajax) }}</li>
	<li>{{ link_to_route('filetypes.index', 'Filetype Editor', null, $noajax) }}</li>
	<li>{{ link_to_route('list_judges', 'User List', null, $noajax) }}</li>
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