@extends('layouts.scaffold')

@section('style')
.div_row th {
	background-color: #428BCA !important;
	color: white;
}

.div_row th:first-child {
	font-weight: bold;
}

.score_row td {
	text-align: center;
}

.score_row td:first-child {
	text-align: left;
	padding-left: 15px;
	width: 60%;
}

@stop

@section('script')
	var myTextExtraction = function(node)
	{
	    // extract data from markup and return it
	    return (node.innerHTML=='-') ? -1 : node.innerHTML ;
	}
@stop

@section('main')
<div class="col-md-8">
@if(count($videos))
	@foreach($videos as $div => $videos)
	<table class="table table-striped table-bordered">
		<thead>
			<tr class="div_row">
				<th class="header">{{ $div }}</th>
				<th class="header">School</th>
			</tr>
		</thead>
		<tbody>
			@foreach($videos as $name => $video)
				<tr class="score_row">
					<td><a href="{{ route('display.show_video', [ $comp->id, $video->id ]) }}" >
							{{ $name }}
						</a>
					</td>
					<td>{{ $video->school->name }}</td>
				</tr>
				@endforeach
		</tbody>
	</table>
	@endforeach
@else
	<h4>No Videos</h4>
@endif

</div>
@stop