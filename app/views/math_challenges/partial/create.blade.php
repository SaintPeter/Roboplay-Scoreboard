@if ($errors->any())
<div class="col-md-12 clearfix">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

{{ Form::open(array('route' => 'math_challenges.store', 'id' => 'math_form')) }}
	<div class="form-group mix clearfix">
		<div class="col-md-6">
			{{ Form::label('order', 'Challenge Number', [ 'class' => 'form-label' ]) }}
			{{ Form::text( 'order', $order, [ 'class' => 'form-control numeric', 'title' => 'For Challenge Ordering' ]) }}
		</div>
		<div class="col-md-6">
			{{ Form::label('level', 'Challenge Level', [ 'class' => 'form-label' ]) }}
			{{ Form::text( 'level', 1, [ 'class' => 'form-control numeric', 'title' => 'For Grouping Purposes' ]) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('display_name', 'Display Name', [ 'class' => 'form-label' ]) }}
		{{ Form::text('display_name', null, [ 'class' => 'form-control' ]) }}
	</div>

	<div class="form-group">
		{{ Form::label('file_name', 'File Name', [ 'class' => 'form-label' ]) }}
		{{ Form::text('file_name', null, [ 'class' => 'form-control' ]) }}
	</div>


	<div class="form-group">
		{{ Form::label('description', 'Description', [ 'class' => 'form-label' ]) }}
		{{ Form::textarea('description', null, [ 'class' => 'form-control', 'cols' => 40, 'rows' => 4 ]) }}
	</div>

	<div class="form-group mix clearfix">
		<div class="col-md-6">
		{{ Form::label('points', 'Points', [ 'class' => 'form-label' ]) }}
		{{ Form::text( 'points', 5, [ 'class' => 'form-control numeric' ]) }}
		</div>
		<div class="col-md-6">
		{{ Form::label('multiplier', 'Multiplier', [ 'class' => 'form-label' ]) }}
		{{ Form::text( 'multiplier', 1.0, [ 'class' => 'form-control decimal' ]) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::input('hidden', 'division_id', $division_id) }}
		{{ Form::submit('Submit', array('class' => 'btn btn-primary math_submit')) }}
	</div>
{{ Form::close() }}


