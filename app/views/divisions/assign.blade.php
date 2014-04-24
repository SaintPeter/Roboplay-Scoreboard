@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('/css/multi-select.css') }}
	{{ HTML::script('/js/jquery.multi-select.js') }}
@stop

@section('script')
	$(function() {
		jQuery("#has_list").multiSelect({
			selectableHeader: "<div class='panel-title'>All Challenges</div>",
  			selectionHeader: "<div class='panel-title'>In Division</div>"
  		});
	});
@stop

@section('main')

<h1>Assign Challenges</h1>
{{ Breadcrumbs::render() }}
{{ Form::open(array('route' =>'divisions.saveassign')) }}
	{{ Form::select('has[]', $all_list, $selected_list, array('id' => 'has_list','multiple'=>'multiple')) }}
	{{ Form::hidden('division_id', $division_id) }}
	{{ Form::submit('Submit', array('class' => 'btn')) }}
{{ Form::close() }}


@stop