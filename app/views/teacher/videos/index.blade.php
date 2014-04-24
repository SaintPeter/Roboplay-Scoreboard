@extends('layouts.scaffold')

@section('main')

<h1>Videos - {{ $school_name }}</h1>
{{ Breadcrumbs::render() }}
<p>{{ link_to_route('teacher.videos.create', 'Add new video') }}</p>

@if ($videos->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th>School</th>
				<th>Video Division</th>
				<th colspan="3">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($videos as $video)
				<tr>
					<td>{{{ $video->name }}}</td>
					<td><a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a></td>
					<td>{{{ $video->has_custom }}}</td>
					<td>{{{ $video->school_name }}}</td>
					<td>{{{ $video->vid_division->longname() }}}</td>
					<td>{{ link_to_route('teacher.videos.show', 'Show', array($video->id), array('class' => 'btn btn-primary')) }}</td>
                    <td>{{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id))) }}
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
