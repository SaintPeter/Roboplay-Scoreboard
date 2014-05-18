@extends('layouts.scaffold')

@section('script')
	var delete_button = '';
	$(function() {
		$("[value|='Delete']").click(function(e) {
			e.preventDefault();
			delete_button = this;
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:550,
			modal: true,
			buttons: {
				"Delete Competition": function() {
					$( this ).dialog( "close" );
					$(delete_button).parent().submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
@stop

@section('main')
<p>{{ link_to_route('competitions.create', 'Add Competition', [], ['class' => 'btn btn-primary']) }}</p>

@if ($competitions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Location</th>
				<th>Address</th>
				<th>Event Date</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($competitions as $competition)
				<tr>
					<td>{{{ $competition->name }}}</td>
					<td>{{{ $competition->description }}}</td>
					<td>{{{ $competition->location }}}</td>
					<td>{{{ $competition->address }}}</td>
					<td>{{{ $competition->event_date }}}</td>
                    <td>{{ link_to_route('competitions.edit', 'Edit', array($competition->id), array('class' => 'btn btn-info btn-margin')) }}
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('competitions.destroy', $competition->id), 'style' => 'display: inline-block')) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger btn-margin')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no competitions
@endif


<div id="dialog-confirm" title="Delete Competition?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This competition, all divisions, all teams, and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop
