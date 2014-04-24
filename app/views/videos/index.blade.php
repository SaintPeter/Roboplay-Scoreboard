@extends('layouts.scaffold')

@section('main')

<h1>All Videos</h1>

<p>{{ link_to_route('videos.create', 'Add new video') }}</p>

@if ($videos->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Yt_code</th>
				<th>Students</th>
				<th>Has_custom</th>
				<th>School_name</th>
				<th>Vid_division_id</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($videos as $video)
				<tr>
					<td>{{{ $video->name }}}</td>
					<td>{{{ $video->yt_code }}}</td>
					<td>{{{ $video->students }}}</td>
					<td>{{{ $video->has_custom }}}</td>
					<td>{{{ $video->school_name }}}</td>
					<td>{{{ $video->vid_division_id }}}</td>
                    <td>{{ link_to_route('videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('videos.destroy', $video->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no videos
@endif

@stop
