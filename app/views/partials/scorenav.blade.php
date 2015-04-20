<div style="margin: 10px 0px;">
	<ul class="nav nav-pills">
	  <li @if($nav == 'reported') class="active" @endif>{{ link_to_route('video_scores.manage.reported', 'Reported Videos') }}</li>
	  <li @if($nav == 'by_judge') class="active" @endif>{{ link_to_route('video_scores.manage.index', 'Scores By Judge') }}</li>
	  <li @if($nav == 'by_video') class="active" @endif>{{ link_to_route('video_scores.manage.by_video', 'Scores By Video') }}</li>
	  <li @if($nav == 'summary') class="active" @endif>{{ link_to_route('video_scores.manage.summary', 'Score Summary') }}</li>
	  <li @if($nav == 'judges') class="active" @endif>{{ link_to_route('video_scores.manage.judge_performance', 'Judge Performance') }}</li>
	</ul>
</div>