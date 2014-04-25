@extends('layouts.scaffold')

@section('head')
{{ HTML::script('js/jquery.jCombo.js') }}
@stop

@section('script')
	var current_user = 0;
	 $(function() {
	 $( "#choose_school" ).hide();
		$( "#choose_school" ).attr("id","choose_school_active").dialog({ autoOpen: false });
		$(".set_school").click(function(e) {
			current_user = $(e.target).attr('user_id');
			$( "#choose_school_active" ).dialog("open");
			$( "#choose_school_active #select_county" ).jCombo({url: "ajax/c"});
			$( "#choose_school_active #select_district" ).jCombo({url: "ajax/d/",
																  parent: "#choose_school_active #select_county",
																  initial_text: "-- Select District --",
																  });
			$( "#choose_school_active #select_school" ).jCombo({url: "ajax/s/",
																  parent: "#choose_school_active #select_district",
																  initial_text: "-- Select School --",
																  });
			//$( "#choose_school" ).clone().attr("id","choose_school_active").dialog({ autoOpen: false });
		});
	});

@stop

@section('main')

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>County</th>
			<th>Division</th>
			<th>School Name</th>
			<th>School Id</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	@foreach($users as $user)
		<tr>
			<td>{{ $user->metadata['first_name'] }} {{ $user->metadata['last_name'] }}</td>
			<td>{{ $user->metadata['wp_county'] }}</td>
			<td>{{ $user->metadata['wp_district'] }}</td>
			<td>{{ $user->metadata['wp_school'] }}</td>
			<td>{{ $user->metadata['school_id'] }}</td>
			<td><button user_id="{{ $user->ID }}" class="btn btn-primary set_school">Set School</button></td>
		</tr>
	@endforeach
	</tbody>
</table>

<div id="choose_school" title="Choose School">
	<table>
		<tr>
			<td>County</td>
			<td><select id="select_county"></select></td>
		</tr>
		<tr>
			<td>District</td>
			<td><select id="select_district"></select></td>
		</tr>
		<tr>
			<td>School</td>
			<td><select id="select_school"></select></td>
		</tr>
		<tr>
			<td colspan="2"><button id="save_school" class="btn btn-primary">Save School</button></td>
		</tr>
	</table>
</div>

@stop