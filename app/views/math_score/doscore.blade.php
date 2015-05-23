@extends('layouts.mobile')

@section('header', 'Math Programming Score')

@section('style')
	.ui-li-static {
		white-space: normal; !important
	}
	.bigtext {
		font-size: 100px;
	}
	.center {
		text-align: center;
	}
	#abortPopup-popup, #submitPopup-popup, #randomPopup-popup {
		width: 90%;
	}
@stop

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

@section('script')

@stop

@section('main')
<div class="ui-body ui-body-a" style="margin-bottom: 2em;">
	<p>
		<strong>Judge: </strong>{{ $judge->display_name }}<br />
		<strong>Division: </strong>{{{ $team->division->name }}}<br />
		<strong>Team: </strong>{{{ $team->longname() }}}
	</p>
	<h2>Run {{{ $run_number }}}</h2>
	<p>
		<strong>{{{ $challenge->display_name }}}</strong><br />
		{{ nl2br($challenge->description) }}
	</p>
</div>

{{ Form::open( [ 'route' => [ 'math_score.save', $team->id, $challenge->id ], 'method' => 'post', 'id' => 'math_form', 'data-ajax' => 'false' ] ) }}
	<ul data-role="listview">
		<li class="ui-field-contain ui-li-static ui-body-inherit ui-first-child" data-type="vertical">
		<p>Score</p>
		<input class="ui-clear-both" name="score" min="0" max="{{ $challenge->points }}" step="1" value="0" type="range">
		</li>
		<li>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">{{ Form::submit('Submit', [ 'class' => 'ui-btn', 'name' => 'submit' ] ) }}</div>
				<div class="ui-block-b">{{ Form::submit('Cancel', [ 'class' => 'ui-btn', 'name' => 'cancel' ] ) }}</div>
			</fieldset>
		</li>
		<input type="hidden" name="changeme" id="submit_action" value="1">
	</ul>
{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

<script>
	$("#back_button").attr("href", "{{ route('math_score.score_team', [$competition_id, $division_id, $team->id]) }}");
</script>

@stop