@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
@stop

@section('script')
	$(function() {
		$("#user_table").filterTable();
	});
@stop

@section('main')
<table class="table table-striped table-bordered" id="user_table">
	<thead>
		<tr>
			<th>User</th>
			<th>Username</th>
			<th>E-mail</th>
			<th>User Id</th>
			<th>Is Judge?</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($judges as $judge)
		<tr>
			<td>{{$judge->display_name }}</td>
			<td>{{$judge->username }}</td>
			<td>{{ $judge->email }}</td>
			<td>{{ $judge->id }}</td>
			<td>{{ $judge->is_judge }}</td>
			<td>{{ link_to_route('switch_user', 'Switch To', [ $judge->id ], [ 'class' => 'btn btn-primary' ]) }}</td>
		</tr>
		@endforeach
	</tbody>

@stop