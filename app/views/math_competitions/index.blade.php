@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('math_competitions.create', 'Add Math Competition',[], ['class' => 'btn btn-primary']) }}</p>


<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Event Date</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
	@if ($math_competitions->count())
		@foreach ($math_competitions as $math_competition)
			<tr>
				<td>{{{ $math_competition->name }}}</td>
				<td>{{{ $math_competition->event_date }}}</td>
                <td>{{ link_to_route('math_competitions.edit', 'Edit', array($math_competition->id), array('class' => 'btn btn-info btn-margin')) }}

                    {{ Form::open(array('method' => 'DELETE', 'route' => array('math_competitions.destroy', $math_competition->id), 'style' => 'display: inline-block')) }}
                        {{ Form::submit('Delete', array('class' => 'btn btn-danger btn-margin')) }}
                    {{ Form::close() }}
                </td>
			</tr>
		@endforeach
	@else
		<tr><td colspan="3" class="text-center">There are no Math Competitions</td></tr>
	@endif
	</tbody>
</table>

@stop
