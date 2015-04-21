<div style="margin: 10px 0px;">
	<ul class="nav nav-pills">
		<li @if($nav == 'by_judge') class="active" @endif>{{ link_to_route('video_scores.manage.index', 'Scores By Judge', [ $year ]) }}</li>
		<li @if($nav == 'reported') class="active" @endif>{{ link_to_route('video_scores.manage.reported', 'Reported Videos', [ $year ]) }}</li>
		<li @if($nav == 'by_video') class="active" @endif>{{ link_to_route('video_scores.manage.by_video', 'Scores By Video', [ $year ]) }}</li>
		<li @if($nav == 'summary') class="active" @endif>{{ link_to_route('video_scores.manage.summary', 'Score Summary', [ $year ]) }}</li>
		<li @if($nav == 'judges') class="active" @endif>{{ link_to_route('video_scores.manage.judge_performance', 'Judge Performance', [ $year ]) }}</li>
	</ul>
</div>