<form id="student_list">
<table class="table table-condensed">
	<tbody>
		@if(count($student_list) > 0)
			@foreach($student_list as $student)
			<tr>
				<td class="text-center">{{ Form::checkbox('students[]', $student->id) }}</td>
				<td>{{ $student->fullName() }}</td>
			</tr>
			@endforeach
		@else
			<tr>
				<td>All Students are currently assigned.</td>
			</tr>
		@endif
	</tbody>
</table>
</form>