		@if ($challenges->count())
			@foreach ($challenges as $challenge)
				<tr id="challenge_{{ $challenge->id }}">
					<td style="white-space:nowrap;"><span></span>{{{ $challenge->pivot->display_order }}} </td>
					<td>{{{ $challenge->internal_name }}}</td>
					<td>{{{ $challenge->display_name }}}</td>
					<td>{{{ $challenge->rules }}}</td>
					<td>{{{ $challenge->score_elements->count() }}}</td>
                    <td>{{ link_to_route('challenges.show', 'Show', array($challenge->id), array('class' => 'btn btn-info')) }}</td>
                    <td>{{ link_to_route('divisions.removeChallenge', 'Remove', array($division->id, $challenge->id), array('class' => 'btn btn-danger')) }}</td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="6">There are no Challenges</td></tr>
		@endif