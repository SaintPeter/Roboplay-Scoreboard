<!-- student.partial.create_empty -->
<div class="vertical-container row student_{{$index}}">
	@if($index > 0)
		<hr>
	@endif
	<div class="col-md-12">
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][first_name]">First Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][first_name]" name="students[{{ $index }}][first_name]" placeholder="First Name">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][first_name]">Middle/Nick Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][middle_name]" name="students[{{ $index }}][middle_name]" placeholder="Middle/Nick">
		</div>
		<div class="checkbox">
		    <label>
		      <input type="hidden" name="students[{{ $index }}][nickname]" value="0">
		      <input type="checkbox" name="students[{{ $index }}][nickname]" value="1"> Is Nickname
		    </label>
		  </div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][last_name]">Last Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][last_name]" name="students[{{ $index }}][last_name]" placeholder="Last Name">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][ssid]">Social Security</label>
			<input type="text" class="form-control" id="students[{{ $index }}][ssid]" name="students[{{ $index }}][ssid]" placeholder="State Student ID">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][gender]">Gender</label>
			{{ Form::select("students[$index][gender]", [ 0 => '- Pick Gender -', 'Male' => 'Male', 'Female' => 'Female' ], null, [ 'class' => 'form-control' ] ) }}
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][ethnicity_id]">Ethnicity</label>
			{{ Form::select("students[$index][ethnicity_id]", $ethnicity_list, null, [ 'class' => 'form-control' ] ) }}
		</div>
		<div class="form-group">
			<label for="students[{{ $index }}][grade]">Grade
			{{ Form::selectRange("students[$index][grade]", 5, 14,null, [ 'class' => 'form-control' ] ) }}
			</label>
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][email]">E-mail</label>
			<input type="text" class="form-control" id="students[{{$index}}][email]" name="students[{{$index}}][email]" placeholder="E-mail">
		</div>
		<div class="form-group">
		    <label class="sr-only" for="students[{{ $index }}][math_level_id]">Math Level</label>
		    {{ Form::select("students[$index][math_level_id]", Math_Level::getList(), null, [ 'class' => 'form-control' ] ) }}
		</div>
		<div class="form-group">
			{{ Form::select("students[$index][tshirt]", [ 0 => '- Pick T-shirt Size -', 'XS' => 'XS - Extra Small', 'S' => 'S - Small', 'M' => 'M - Medium', 'L' => 'L - Large', 'XL' => 'XL - Extra Large', 'XXL' => 'XXL - Extra, Extra Large' ], null, [ 'class' => 'form-control' ] ) }}
		</div>
	</div>
	<div class="col-md-1 text-center">
		<button type="button" class="btn btn-danger remove_student" aria-label="Remove Student" index="{{$index}}" title="Remove Student">
  			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
		</button>
	</div>
</div>