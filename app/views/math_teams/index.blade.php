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

		$("#math_team_table").filterTable();
	});
@stop

@section('main')
@include('partials.year_select')
<p>{{ link_to_route('math_teams.create', 'Add Math Team', [], [ 'class' => 'btn btn-primary' ]) }}</p>

<table id="math_team_table" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>Teacher/County/District/School</th>
			<th>Year</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($math_teams->count())
			@foreach ($math_teams as $math_team)
			<tr>
				<td>{{{ $math_team->name }}}</td>
				<td>{{ join('<br />', $math_team->student_list()) }}</td>
				<td>{{{ $math_team->division->longname() }}}</td>
				<td>
					@if(isset($math_team->school))
						{{ isset($math_team->teacher) ? $math_team->teacher->getName() : 'Not Set' }}<br />
						<strong>C:</strong> {{ $math_team->school->district->county->name }}<br />
						<strong>D:</strong> {{ $math_team->school->district->name }}<br />
						<strong>S:</strong> {{ $math_team->school->name }}
					@else
						Not Set
					@endif
				</td>
				<td>{{ $math_team->year }}</td>
                <td>
                	{{ link_to_route('math_teams.show', 'Show', [$math_team->id], [ 'class' => 'btn btn-default' ]) }}
                	&nbsp;
                	{{ link_to_route('math_teams.edit', 'Edit', array($math_team->id), array('class' => 'btn btn-info')) }}
                	&nbsp;
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('math_teams.destroy', $math_team->id), 'id' => 'delete_form_' . $math_team->id, 'style' => 'display: inline-block;')) }}
                        {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $math_team->id)) }}
                    {{ Form::close() }}
                </td>
                </td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="6" class="text-center">No Math Teams Created</td></tr>
		@endif
	</tbody>
</table>

<div id="dialog-confirm" title="Delete Math Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This math_team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop
