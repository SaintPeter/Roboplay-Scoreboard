@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.jCombo.js') }}
@stop

@section('script')
$(function() {
	$( "#select_county" ).jCombo({url: "/scoreboard/ajax/c",
			initial_text: "-- Select County --"
		});
	$( "#select_district" ).jCombo({url: "/scoreboard/ajax/d/",
			parent: "#select_county",
			initial_text: "-- Select District --",
		});
	$( "#select_school" ).jCombo({url: "/scoreboard/ajax/s/",
			parent: "#select_district",
			initial_text: "-- Select School --",
		});
});
@stop


@section('main')

<h1>Add Team</h1>
{{ Breadcrumbs::render() }}
{{ Form::open(array('route' => 'teams.store', 'role'=>"form", 'class' => 'col-md-6')) }}
        <div class="form-group">
            {{ Form::label('name', 'Team Name:') }}
            {{ Form::text('name','', array('class'=>'form-control col-md-4')) }}
        </div>

        <div class="form-group">
        	{{ Form::label('division_id', 'Division:') }}
            {{ Form::select('division_id', $divisions, '', [ 'class'=>'form-control col-md-4' ]) }}
        </div>

        <div class="form-group">
			<label for="select_county">County</label>
			<select name="select_county" id="select_county" class="form-control col-md-4"></select>
		</div>

		<div class="form-group">
			<label for="select_district">District</label>
			<select name="select_district" id="select_district" class="form-control col-md-4"></select>
		</div>

		<div class="form-group">
			<label for="select_school">School</label>
			<select name="school_id" id="select_school" class="form-control col-md-4"></select>
		</div>

        <div class="form-group">
            {{ Form::label('students', 'Students:') }}
            {{ Form::textarea('students', '' , array('class'=>'form-control col-md-4')) }}
            <p>Enter one student per line.</p>
        </div>

 		{{ Form::submit('Submit', array('class' => 'btn btn-primary ')) }}
 		&nbsp;
 		{{ link_to_route('teams.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{{ Form::close() }}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@stop


