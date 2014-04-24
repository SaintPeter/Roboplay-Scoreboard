@extends('layouts.scaffold')

@section('main')

<h1>All Video Divisions</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('vid_divisions.create', 'Add New Video Division') }}</p>

@if ($vid_divisions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Display Order</th>
				<th>Competition</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($vid_divisions as $vid_division)
				<tr>
					<td>{{{ $vid_division->name }}}</td>
					<td>{{{ $vid_division->description }}}</td>
					<td>{{{ $vid_division->display_order }}}</td>
					<td>{{{ $vid_division->competition->name }}}</td>
                    <td>{{ link_to_route('vid_divisions.edit', 'Edit', array($vid_division->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('vid_divisions.destroy', $vid_division->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no vid_divisions
@endif

@stop
