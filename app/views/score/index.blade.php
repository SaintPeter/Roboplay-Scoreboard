@extends('layouts.mobile')

@section('header', 'Select Challenge')

@section('navbar')
<a class="ui-btn-right"
   href="#"
   data-icon="back"
   data-iconpos="notext"
   role="button"
   id="back_button"
   data-ajax="false"
   >Back</a>
@stop

@section('main')
<div id="menu-anchor"></div>
<div class="ui-body ui-body-a ui-corner-all">
	<strong>Judge: </strong>{{ $judge->display_name }}<br />
	<strong>Team: </strong>{{{ $team->longname() }}}
</div>
<ul class="ui-listview ui-listview-inset ui-corner-all" data-role="listview" data-filter="true">
	@foreach($challenges as $challenge)
		<li>
			<a href="{{ route('score.doscore', array($team_id, $challenge->id)) }}" data-ajax="false">
			{{ $challenge->pivot->display_order }}.&nbsp;{{ $challenge->display_name }} <span class="ui-li-count">{{ $challenge->run_count($team_id) }}</span> <br />
			@if($challenge->run_count($team_id) > 0)
				<p>
					<strong>Last Score:</strong> {{ $challenge->runs($team_id)->last()->total }}
					<strong>Best Score:</strong> {{ $challenge->runs($team_id)->max('total') }}
				</p>
			@else

			@endif
			</a>
		</li>
	@endforeach
</ul>

<div class="ui-body ui-body-a ui-corner-all">
	<a href="{{ route('display.teamscore', $team_id) }}" class="ui-btn" data-ajax="false">Show Team Score</a>
</div>

<script>
	$("#back_button").attr("href", "{{ route('score.choose_team', [$competition_id, $division_id]) }}");
</script>

@stop