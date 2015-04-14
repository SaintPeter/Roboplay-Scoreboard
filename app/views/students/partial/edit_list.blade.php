@if(!empty($students))
	@foreach($students as $index => $student)
		@include('students.partial.create',	compact('index', 'ethnicity_list', 'student' ))
	@endforeach
@endif