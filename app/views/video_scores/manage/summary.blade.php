@extends('layouts.scaffold')

@section('main')
<h1>Scoring Summary</h1>
{{ Breadcrumbs::render() }}
<table class="col-md-4">
	@foreach($output as $comp => $divs)
		<tr class="comp_row"><td>{{ $comp }}</td></tr>
		@foreach($divs as $div => $videos)
			<tr  class="div_row"><td>{{ $div }}</td></tr>
			@foreach($videos as $video)
			<tr class="score_row">
				<td>{{ $video->name }}</td>
				<td>{{ count($video->scores) }}</td>
			</tr>
			@endforeach
		@endforeach
	@endforeach
</table>

@stop