@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
@stop

@section('style')
.ui-widget-overlay {
    background: url('http://code.jquery.com/ui/1.10.4/themes/smoothness/images/ui-bg_flat_0_aaaaaa_40x100.png') repeat-x scroll 100% 100% #AAA;
    opacity: 0.3;
}
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

		$("#team_table").filterTable();
	});
@stop

@section('main')
<p>{{ link_to_route('teams.create', 'Add Team', [], [ 'class' => 'btn btn-primary' ]) }}</p>

<table id="team_table" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($teams->count())
			@foreach ($teams as $team)
			<tr>
				<td>{{{ $team->name }}}</td>
				<td>{{ $team->student_count() }}</td>
				<td>{{{ $team->division->longname() }}}</td>
				<td>
					@if(isset($team->school))
						<strong>C:</strong> {{ $team->school->district->county->name }}<br />
						<strong>D:</strong> {{ $team->school->district->name }}<br />
						<strong>S:</strong> {{ $team->school->name }}
					@else
						Not Set
					@endif
				</td>
                <td>
                	{{ link_to_route('teams.show', 'Show', [$team->id], [ 'class' => 'btn btn-default' ]) }}
                	&nbsp;
                	{{ link_to_route('teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}
                	&nbsp;
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teams.destroy', $team->id), 'id' => 'delete_form_' . $team->id, 'style' => 'display: inline-block;')) }}
                        {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $team->id)) }}
                    {{ Form::close() }}
                </td>
                </td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="5">No Teams Created</td></tr>
		@endif
	</tbody>
</table>

<div id="dialog-confirm" title="Delete Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop
