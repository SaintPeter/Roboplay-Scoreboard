@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/bootstrap-timepicker.min.css') }}
	{{ HTML::script('js/bootstrap-timepicker.min.js') }}
@stop

@section('style')
    .timepicker {
        width: 120px;
    }

@stop

@section('script')
    $(document).on('ready', function() {
        $('.timepicker').timepicker({ defaultTime: 'current' }).on('show.timepicker', function(e) {
            $(this).timepicker('setTime', $(this).val());
        });;
    });
@stop


@section('main')
{{ Form::open([ 'route' => 'schedule.update', 'method' => 'post' ]) }}
<table class="table table-striped table-nonfluid table-condensed">
    <tr>
        <th>Start</th>
        <th>Event</th>
        <th>Action</th>
    </tr>
    @foreach($schedule as $row)
    <tr>
        <td>
            <div class="input-group bootstrap-timepicker timepicker">
                <input name="{{ "schedule[{$row->id}][start]" }}" type="text" class="form-control input-sm timepicker" value="{{ $row->start }}">
                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
            </div>
        </td>
        <td>
            {{ Form::text("schedule[{$row->id}][display]", $row->display, [ 'class' => 'form-control input-sm' ]) }}
        </td>
        <td>
            <button class="btn btn-default btn-sm btn-margin insert_above" title="Insert Row Above">
                <i class="fa fa-plus"></i>
                <i class="fa fa-caret-up"></i>
            </button>
            <button class="btn btn-default btn-sm btn-margin insert_below" title="Insert Row Below">
                <i class="fa fa-plus"></i>
                <i class="fa fa-caret-down"></i>
            </button>
        </td>
    </tr>
    @endforeach
</table>
    <input type="submit" name="submit" value="Save" class="btn btn-primary">
{{ Form::close() }}
@stop