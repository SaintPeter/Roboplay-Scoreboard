@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('math_divisions.index', 'Return to all math_divisions', ['class' => 'btn btn-primary']) }}</p>

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
		<tr>
			<td>{{{ $math_division->name }}}</td>
					<td>{{{ $math_division->description }}}</td>
					<td>{{{ $math_division->display_order }}}</td>
					<td>{{{ $math_division->competition_id }}}</td>
                    <td>{{ link_to_route('math_divisions.edit', 'Edit', array($math_division->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('math_divisions.destroy', $math_division->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
