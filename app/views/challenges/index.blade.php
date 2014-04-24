@extends('layouts.scaffold')

@section('main')

<h1>All Challenges</h1>
{{ Breadcrumbs::render() }}

<p>{{ link_to_route('challenges.create', 'Add new challenge') }}</p>


<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Internal Name</th>
			<th>Display Name</th>
			<th>Rules</th>
			<th>Score Elements</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($challenges->count())
			@foreach ($challenges as $challenge)
				<tr>
					<td>{{{ $challenge->internal_name }}}</td>
					<td>{{{ $challenge->display_name }}}</td>
					<td>{{{ $challenge->rules }}}</td>
					<td>{{{ $challenge->score_elements->count() }}}</td>
                    <td>{{ link_to_route('challenges.show', 'Show', array($challenge->id), array('class' => 'btn')) }}</td>
                    <td>{{ link_to_route('challenges.edit', 'Edit', array($challenge->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('challenges.destroy', $challenge->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="5">There are no Challenges</td></tr>
		@endif
	</tbody>
</table>

@stop
