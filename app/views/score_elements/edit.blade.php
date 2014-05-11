{{ Form::model($score_element, array('method' => 'PATCH', 'route' => array('score_elements.update', $score_element->id), 'id' => 'se_form')) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('display_text', 'Display Text') }}
            {{ Form::text('display_text') }}
        </li>

        <li>
            {{ Form::label('element_number', 'Display Order') }}
            {{ Form::input('number', 'element_number') }}
        </li>

        <li>
            {{ Form::label('base_value', 'Base Value') }}
            {{ Form::input('number', 'base_value') }}
        </li>

        <li>
            {{ Form::label('multiplier', 'Multiplier') }}
            {{ Form::input('number', 'multiplier') }}
        </li>

        <li>
            {{ Form::label('min_entry', 'Minimum Value') }}
            {{ Form::input('number', 'min_entry') }}
        </li>

        <li>
            {{ Form::label('max_entry', 'Maximum Value') }}
            {{ Form::input('number', 'max_entry') }}
        </li>

        <li>
            {{ Form::label('type', 'Input Type') }}
            {{ Form::select('type', $input_types) }}
        </li>

		<li>
			{{ Form::input('hidden', 'challenge_id') }}
			{{ Form::submit('Update', array('class' => 'btn btn-info se_submit')) }}
			{{ link_to_route('score_elements.show', 'Cancel', $score_element->id, array('class' => 'btn se_close')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif
