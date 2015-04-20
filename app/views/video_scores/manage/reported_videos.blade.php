@extends('layouts.scaffold')

@section('style')
.reported th {
	background-color: #428BCA;
	color: white;
	padding: 2px;
}

.reported th:first-child {
	text-align: left !important;
	width: 150px;
}

tr.score_row:nth-child(odd){
	background-color: #FAFAFA;
}

.reported td {
	width: 200px;
	border: 1px solid lightgrey;
	vertical-align: top;
}

.reported tbody td:first-child {
	width: 200px !important;
	text-align: left !important;
	padding-left: 12px;
}
@stop


@section('main')
@include('partials.scorenav', [ 'nav' => 'reported' ])

{{ Form::open([ 'route' => 'video_scores.manage.process_report' ]) }}
<table class="table-bordered reported">
	<thead>
		<th>Video</th>
		<th>Reported By</th>
		<th>Comment</th>
		<th>Response</th>
		<th>Action</th>
	</thead>
	<tbody>
		@if(!empty($comments_reported))
			@foreach($comments_reported as $comment)
			<tr class="score_row">
				<td>{{ link_to_route('video.judge.show', $comment->video->name, [ $comment->video->id ]) }}</td>
				<td>{{ $comment->judge->display_name }}</td>
				<td>{{ $comment->comment }}</td>
				<td>{{ Form::textarea('comment', null, [ 'cols' => 40, 'rows' => 4 ]) }}</td>
				<td class="text-center">
					{{ Form::button('Absolve', [ 'type' => 'submit', 'name'=> 'absolve', 'value' => $comment->id ,'class' => 'btn btn-success' ]) }}
					{{ Form::button('Disqualify', [ 'type' => 'submit', 'name'=> 'dq',  'value' => $comment->id ,'class' => 'btn btn-danger' ]) }}</td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="5" class="text-center">No Reported Videos</td></tr>
		@endif
	</tbody>
</table>
{{ Form::close() }}
@stop