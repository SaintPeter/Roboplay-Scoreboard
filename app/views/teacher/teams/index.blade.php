@extends('layouts.scaffold')

@section('style')
.Paid {
	background-color: lightgreen;
}

.Unpaid {
	background-color: pink;
}

.ui-widget-overlay {
    background: url('images/ui-bg_flat_0_aaaaaa_40x100.png') repeat-x scroll 100% 100% #AAA;
    opacity: 0.3;
}

.summary_table {
	width: 500;
	background-color: white;
}

.clear { clear: both; }

@stop

@section('script')
	var delete_id = 0;
	$(function() {
		$(".delete_button").click(function(e) { 
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#dialog-confirm").dialog('open');		
		});
	
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete Team": function() {
					$( this ).dialog( "close" );
					$('#delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
@stop

@section('main')

<div class="info_header">
	<div class="summary_table pull-right" >
		<table class="table table-condensed table-bordered">
			<thead>
				<tr>
					<th>County/District/School</th>
					<th>Challenge/Division</th>
					<th>Teams (Used)</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>C:</strong> {{ $invoice->school->district->county->name }}<br />
						<strong>D:</strong> {{ $invoice->school->district->name }}<br />
						<strong>S:</strong> {{ $invoice->school->name }}
					</td>
					<td>
						{{ $invoice->challenge_division->competition->name }}<br />
						{{ $invoice->challenge_division->name }}
					</td>
					<td class="text-center">{{ $invoice->team_count }} ({{ $teams->count() }})</td>
					<td class="{{ $paid }}">{{ $paid }}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<h1>Challenge Teams</h1>
	<h2>{{ $school->name }}</h2>
	<div class="clear"></div>
</div>
{{ Breadcrumbs::render() }}

@if( $teams->count() < $invoice->team_count AND $invoice->team_count > 0)
	@if($invoice->paid == 1)
		<p>{{ link_to_route('teacher.teams.create', 'Add Team',array(), array('class' => 'btn btn-primary')) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Team Limit Reached</p>
@endif

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($teams->count())
			@foreach ($teams as $team)
				<tr>
					<td>{{{ $team->name }}}</td>
					<td>{{ nl2br($team->students) }}</td>
	                <td>{{ link_to_route('teacher.teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}
	                	&nbsp;
	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.teams.destroy', $team->id), 'id' => 'delete_form_' . $team->id, 'style' => 'display: inline-block;')) }}
	                        {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $team->id)) }}
	                    {{ Form::close() }}
	                </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="3">No Teams Created</td></tr>
		@endif

	</tbody>
</table>

<div id="dialog-confirm" title="Delete Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop
