@extends('layouts.scaffold')

@section('main')

<h1>All Divisions</h1>

{{ Breadcrumbs::render() }}

<p>{{ link_to_route('divisions.create', 'Add New Division',array(), array('class' => 'btn btn-primary')) }}</p>

@if ($divisions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Order</th>
				<th>Competition</th>
				<th>Challenges</th>
				<th colspan="3">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($divisions as $division)
				<tr>
					<td>{{{ $division->name }}}</td>
					<td>{{{ $division->description }}}</td>
					<td>{{{ $division->display_order }}}</td>
					<td>{{{ $division->competition->name }}}</td>
					<td>{{{ $division->challenges->count() }}}</td>
					<td>{{ link_to_route('divisions.show', 'Show', array($division->id), array('class' => 'btn btn-default')) }}</td>
                    <td>{{ link_to_route('divisions.edit', 'Edit', array($division->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('divisions.destroy', $division->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no divisions
@endif

@stop
