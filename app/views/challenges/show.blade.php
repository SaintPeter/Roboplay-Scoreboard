@extends('layouts.scaffold')

@section('script')
$(function() {
	$( "#dialog" ).dialog({ autoOpen: false });

	// Add Score Element Button
	$("#add_score_element").click(function() {
		$.get("{{ route('score_elements.create', $challenge->id) }}",
			function( data ) {
				$( "#dialog" ).html(data);
				$( "#dialog" ).dialog("open");
			}, "html" ).done(setup_form_handler);
	});

	// Edit Score Element Button Functions
	$(".btn_se_edit").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#dialog" ).html( data );
			$( "#dialog" ).dialog("open");
			}, "html" ).done(setup_form_handler);
	});
});

function setup_form_handler() {
	jQuery( "#se_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#dialog").html(data);
				setup_form_handler();
			}
		}, "html" );
	});
	jQuery( ".se_close" ).click( function( event ) {
		event.preventDefault();
		jQuery( "#dialog" ).dialog("close");
	});
}

@stop


@section('main')

<h1>Show Challenge</h1>

{{ Breadcrumbs::render() }}

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Internal_name</th>
				<th>Display_name</th>
				<th> Rules</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $challenge->internal_name }}}</td>
					<td>{{{ $challenge->display_name }}}</td>
					<td>{{{ $challenge-> rules }}}</td>
                    <td>{{ link_to_route('challenges.edit', 'Edit', array($challenge->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('challenges.destroy', $challenge->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Display_text</th>
				<th>Order</th>
				<th>Base</th>
				<th>Multiplier</th>
				<th>Min</th>
				<th>Max</th>
				<th>Type</th>
		</tr>
	</thead>

	<tbody>
		@if( $challenge->score_elements->count() == 0 )
		<tr><td colspan=8 style="align=center;">No Score Elements</td></tr>
		@else
		@foreach( $challenge->score_elements as $score_element)
			<tr>
				<td>{{{ $score_element->name }}}</td>
						<td>{{{ $score_element->display_text }}}</td>
						<td>{{{ $score_element->element_number }}}</td>
						<td>{{{ $score_element->base_value }}}</td>
						<td>{{{ $score_element->multiplier }}}</td>
						<td>{{{ $score_element->min_entry }}}</td>
						<td>{{{ $score_element->max_entry }}}</td>
						<td>{{{ $score_element->type }}}</td>
	                    <td>{{ link_to_route('score_elements.edit', 'Edit', array($score_element->id), array('class' => 'btn btn-info btn_se_edit')) }}</td>
	                    <td>
	                        {{ Form::open(array('method' => 'DELETE', 'route' => array('score_elements.destroy', $score_element->id))) }}
	                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
	                        {{ Form::close() }}
	                    </td>
			</tr>
		@endforeach
		@endif
	</tbody>
</table>
{{ Form::button('Add Score Element', array('class' => 'btn', 'id' => 'add_score_element')) }}

<div id="dialog" title="Basic dialog">

</div>

@stop
