@extends('layouts.scaffold')

@section('main')
@foreach($users as $user) 
	<pre>{{ var_dump($user->metadata) }}</pre><br />
@endforeach
<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Division</th>
				<th>School Name</th>
			</tr>
		</thead>

		<tbody>
		</tbody>
	</table>

@stop