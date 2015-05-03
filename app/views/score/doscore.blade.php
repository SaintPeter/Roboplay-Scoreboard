@extends('layouts.mobile')

@section('header', 'Score')

@section('style')
	.ui-li-static {
		white-space: normal; !important
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
	var val = "";
	$(function() {
		// Setup a function to recalculate the score on every "stop" event
		$("[id^=sel_]").change(calculate_score);
		calculate_score();
	});

	function calculate_score() {
		$('#score').html = "Blah";
		var total = 0;
		$('[id^=sel_]').each(function(i, obj) {
			var base = parseInt($(obj).attr('base'));
			var val = parseInt($(obj).val());
			var multi = parseInt($(obj).attr('multi'));
			total += base + ( val * multi);
		});
		total = Math.max(total, 0);
		$('#score').html(total);
	}

@stop

@section('main')
<div class="ui-body ui-body-a">
	<p>
		<strong>Judge: </strong>{{ $judge->display_name }}<br />
		<strong>Division: </strong>{{{ $team->division->name }}}<br />
		<strong>Team: </strong>{{{ $team->longname() }}}
	</p>
	<h2>Run {{{ $run_number }}}</h2>
	<p>
		<strong>{{{ $challenge->display_name }}}</strong><br />
		{{ nl2br($challenge->rules) }}
	</p>
</div>

@if(count($challenge->randoms) > 0)
<div class="ui-body ui-body-a">
	<a href="#randomPopup" id="random_popout" data-rel="popup" data-position-to="window" class="ui-btn ui-btn-inline pull-right">Popout</a>
	<h4>Randoms</h4>
	<p>
	@foreach($challenge->randoms as $random)
		{{ $random->formatted() }}<br />
	@endforeach
	</p>
</div>
<div data-role="popup" id="randomPopup" class="ui-corner-all">
	<div style="padding: 10px 20px">
		<h1>Random Numbers</h1>
		@foreach($challenge->randoms as $random)
			<span style="font-size: 72px;">{{ $random->formatted() }}</span><br />
		@endforeach
	</div>
</div>
@endif
<br />
{{ Form::open(array('route' => array('score.save', $team->id, $challenge->id), 'id' => 'se_form', 'data-ajax' => 'false' )) }}
	<ul data-role="listview">
		@foreach($score_elements as $id => $score_element)
			@if ($score_element->type == 'yesno')
				@include('score.partial.yesno', compact('score_element'))
			@elseif ($score_element->type == 'noyes')
				@include('score.partial.noyes', compact('score_element'))
			@elseif ($score_element->type == 'slider' OR $score_element->type == 'low_slider')
				@include('score.partial.low_slider', compact('score_element'))
			@elseif ($score_element->type == 'high_slider')
				@include('score.partial.high_slider', compact('score_element'))
			@elseif ($score_element->type == 'score_slider')
				@include('score.partial.score_slider', compact('score_element'))
			@else
				<li>Error displaying Score Element '{{ $score_element->display_text }} ({{ $score_element->element_number }})'</li>
			@endif
		@endforeach
		<li>
			Estimated Score: <span id="score"></span> out of {{ $challenge->points }} points
		</li>
		<li>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">{{ Form::submit('Submit', array('class' => 'ui-btn', 'name' => 'submit')) }}</div>
				<div class="ui-block-b">{{ Form::submit('Cancel', array('class' => 'ui-btn', 'name' => 'cancel')) }}</div>
			</fieldset>
		</li>
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
	$("#back_button").attr("href", "{{ route('score.score_team', [$competition_id, $division_id, $team->id]) }}");
</script>

@stop