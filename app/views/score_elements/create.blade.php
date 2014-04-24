{{ Form::open(array('route' => 'score_elements.store', 'id' => 'se_form')) }}
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
            {{ Form::input('number', 'element_number', $order) }}
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
			{{ Form::input('hidden', 'challenge_id', $challenge_id) }}
			{{ Form::submit('Submit', array('class' => 'btn btn-info se_submit')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif
