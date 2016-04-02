@extends('layouts.scaffold')

@section('script')
$(function() {
	$( "#dialog" ).clone().attr('id', 'active_dialog').dialog({ autoOpen: false });


};

@endsection

@section('main')
@if(isset($compyears))
    @foreach($compyears as $compyear)
        <h1>{{ $compyear->year }}</h1>
        <p>{{ link_to_route('vid_competitions.create', 'Add Video Competition',[], ['class' => 'btn btn-primary']) }}</p>

        <table class="table table-striped table-bordered">
        	<thead>
        		<tr>
        			<th>Name</th>
        			<th>Start Date</th>
        			<th>End Date</th>
        			<th>Actions</th>
        		</tr>
        	</thead>

        	<tbody>
        		@if ($compyear->vid_competitions->count())
        			@foreach ($compyear->vid_competitions as $vid_competition)
        				<tr>
        					<td>{{{ $vid_competition->name }}}</td>
        					<td>{{{ $vid_competition->event_start }}}</td>
        					<td>{{{ $vid_competition->event_end }}}</td>
        	                <td>{{ link_to_route('vid_competitions.edit', 'Edit', array($vid_competition->id), array('class' => 'btn btn-info btn-margin')) }}

        	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('vid_competitions.destroy', $vid_competition->id), 'style' => 'display: inline-block')) }}
        	                        {{ Form::submit('Delete', array('class' => 'btn btn-danger btn-margin')) }}
        	                    {{ Form::close() }}
        	                </td>
        				</tr>
                        <tr>
                            <td colspan="4">
                                <p>{{ link_to_route('vid_divisions.create', 'Add New Video Division', null, [ 'class' => 'btn btn-primary' ]) }}</p>

                                <table class="table table-striped table-bordered">
                                	<thead>
                                		<tr>
                                			<th>Name</th>
                                			<th>Description</th>
                                			<th>Display Order</th>
                                			<th>Competition</th>
                                			<th>Actions</th>
                                		</tr>
                                	</thead>

                                	<tbody>
                                		@if ($vid_competition->divisions->count())
                                			@foreach ($vid_competition->divisions as $vid_division)
                                				<tr>
                                					<td>{{{ $vid_division->name }}}</td>
                                					<td>{{{ $vid_division->description }}}</td>
                                					<td>{{{ $vid_division->display_order }}}</td>
                                					<td>{{{ $vid_division->competition->name }}}</td>
                                                    <td>{{ link_to_route('vid_divisions.edit', 'Edit', array($vid_division->id), array('class' => 'btn btn-info')) }}
                                                        {{ Form::open(array('method' => 'DELETE', 'route' => array('vid_divisions.destroy', $vid_division->id), 'style' => 'display:inline;')) }}
                                                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                                                        {{ Form::close() }}
                                                    </td>
                                				</tr>
                                			@endforeach
                                		@else
                                			<tr><td colspan="5"  class="text-center">No Video Divisions</td></tr>
                                		@endif
                                	</tbody>
                                </table>
                            </td>
        			@endforeach
        		@else
        			<tr><td colspan="4">There are no Video Competitions</td></tr>
        		@endif
        	</tbody>
        </table>
    @endforeach
@else
<h4>No Comp Years Defined</h4>
@endif

<div id="dialog" title="Score Elements">

</div>

@stop
