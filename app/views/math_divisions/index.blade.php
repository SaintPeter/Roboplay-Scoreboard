@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('math_divisions.create', 'Add New Math Division', null, [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Display Order</th>
			<th>Competition</th>
			<th>Challenges</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($math_divisions->count())
			@foreach ($math_divisions as $math_division)
				<tr>
					<td>{{{ $math_division->name }}}</td>
					<td>{{{ $math_division->display_order }}}</td>
					<td>{{{ $math_division->competition->name }}}</td>
					<td>{{ $math_division->challenges->count() }}</td>
                    <td>
                    	{{ link_to_route('math_divisions.show', 'Show', array($math_division->id), array('class' => 'btn btn-default')) }}
                    	{{ link_to_route('math_divisions.edit', 'Edit', array($math_division->id), array('class' => 'btn btn-info')) }}
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('math_divisions.destroy', $math_division->id), 'style' => 'display:inline;')) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="5" class="text-center">No Math Divisions</td></tr>
		@endif
	</tbody>
</table>
@stop
