@extends('layouts.scaffold')

@section('script')
	$(function() {
		$( "#dialog" ).clone().attr('id', 'challenge_dialog').dialog({ autoOpen: false, width: 350, open: set_max_height });

		// Add Score Element Button
		$("#add_math_challenge").click(function() {
			$.get("{{ route('math_challenges.create', $math_division->id) }}",
				function( data ) {
					$( "#challenge_dialog" ).html(data);
					$( "#challenge_dialog" ).dialog("open");
				}, "html" ).done(setup_form_handler);
		});

		// Edit Score Element Button Functions
		$(".btn_challenge_edit").click( function (event) {
			event.preventDefault();
			$.get( $(this).attr('href'),
				function( data ) {
					$( "#challenge_dialog" ).html( data );
					$( "#challenge_dialog" ).dialog('option', 'title', 'Edit Challenge');
					$( "#challenge_dialog" ).dialog("open");
				}, "html" ).done(setup_form_handler);
		});


	});

function setup_form_handler() {
	jQuery( ".numeric" ).spinner();
	jQuery( ".decimal" ).spinner({numberFormat: "n"});
	jQuery( "#math_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#challenge_dialog").html(data);
				setup_form_handler();
			}
		}, "html" );
	});
	jQuery( ".math_close" ).click( function( event ) {
		event.preventDefault();
		jQuery( "#challenge_dialog" ).dialog("close");
		jQuery( "#challenge_dialog" ).remove();
		$( "#dialog" ).clone().attr('id', 'challenge_dialog').dialog({ autoOpen: false, width: 350, open: set_max_height  });
	});
} // end setup_form_handler

function set_max_height() {
	$(this).css({ "max-height": $(window).height()*0.85, 'overflow-y': 'auto'});
	$(this).dialog('option', 'maxHeight', $(window).height()*0.85);
} // end set_max_height
@stop

@section('style')
.mix .col-md-6 {
	padding-left: 0px;
}
@stop

@section('main')
<p>{{ link_to_route('math_divisions.index', 'Return to Math Divisions', null, ['class' => 'btn btn-primary']) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Display Order</th>
			<th>Competition</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $math_division->name }}}</td>
			<td>{{{ $math_division->display_order }}}</td>
			<td>{{{ $math_division->competition_id }}}</td>
			<td>{{ link_to_route('math_divisions.edit', 'Edit', [ $math_division->id ], [ 'class' => 'btn btn-info' ] ) }}
				{{ Form::open( [ 'method' => 'DELETE', 'route' => [ 'math_divisions.destroy', $math_division->id ], 'style' => 'display: inline' ]) }}
					{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
				{{ Form::close() }}
			</td>
		</tr>
	</tbody>
</table>

<h2>Challenges</h2>
{{ Form::button('Add Math Challenge', [ 'class' => 'btn btn-primary btn-margin', 'id' => 'add_math_challenge' ]) }}<br />

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>Display Name</th>
			<th>File Name</th>
			<th>Description</th>
			<th>Points</th>
			<th>Multiplier</th>
			<th>Level</th>
			<th>Year</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	@if(!$math_division->challenges->isEmpty())
		@foreach($math_division->challenges as $challenge)
		<tr>
			<td>{{ $challenge->order }}</td>
			<td>{{ $challenge->display_name }}</td>
			<td>{{ $challenge->file_name }}</td>
			<td>{{ nl2br($challenge->description) }}</td>
			<td>{{ $challenge->points }}</td>
			<td>{{ sprintf('%1.2f', $challenge->multiplier) }}</td>
			<td>{{ $challenge->level }}</td>
			<td>{{ $challenge->year }}</td>
			<td>{{ link_to_route('math_challenges.edit', 'Edit', [ $challenge->id ], array('class' => 'btn btn-info btn_challenge_edit')) }}
				&nbsp;
				{{ Form::open(['method' => 'DELETE', 'route' => ['math_challenges.destroy', $challenge->id], 'style' => 'display: inline-block']) }}
					{{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
				{{ Form::close() }}

			</td>
		</tr>
		@endforeach
	@else
		<tr><td colspan="9" class="text-center">No Challenges Created</td></tr>
	@endif
	</tbody>

<div id="dialog" title="Add Challenge">

</div>

@stop
