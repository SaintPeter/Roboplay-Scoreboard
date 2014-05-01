@extends('layouts.scaffold')

@section('style')
.Paid {
	background-color: lightgreen;
}

.Unpaid {
	background-color: pink;
}

.ui-widget-overlay {
    background: url('images/ui-bg_flat_0_aaaaaa_40x100.png') repeat-x scroll 100% 100% #AAA;
    opacity: 0.3;
}

.summary_table {
	width: 500;
	background-color: white;
}

.clear { clear: both; }

@stop

@section('script')
	var delete_id = 0;
	$(function() {
		$(".delete_button").click(function(e) { 
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#dialog-confirm").dialog('open');		
		});
	
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete video": function() {
					$( this ).dialog( "close" );
					$('#delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
@stop

@section('main')
<div class="info_header">
	<div class="summary_table pull-right" >
		<table class="table table-condensed table-bordered">
			<thead>
				<tr>
					<th>County/District/School</th>
					<th>Challenge/Division</th>
					<th>videos (Used)</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>C:</strong> {{ $invoice->school->district->county->name }}<br />
						<strong>D:</strong> {{ $invoice->school->district->name }}<br />
						<strong>S:</strong> {{ $invoice->school->name }}
					</td>
					<td>
						{{ $invoice->challenge_division->competition->name }}<br />
						{{ $invoice->challenge_division->name }}
					</td>
					<td class="text-center">{{ $invoice->video_count }} ({{ $videos->count() }})</td>
					<td class="{{ $paid }}">{{ $paid }}</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<h1>Challenge videos</h1>
	<h2>{{ $school->name }}</h2>
	<div class="clear"></div>
</div>
{{ Breadcrumbs::render() }}

@if( $videos->count() < $invoice->video_count AND $invoice->video_count > 0)
	@if($invoice->paid == 1)
		<p>{{ link_to_route('teacher.videos.create', 'Add Video', [], [ 'class' => 'btn btn-primary' ]) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Video Limit Reached</p>
@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th colspan="3">Actions</th>
			</tr>
		</thead>

		<tbody>
			@if ($videos->count())
				@foreach ($videos as $video)
					<tr>
						<td>{{{ $video->name }}}</td>
						<td><a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a></td>
						<td>{{{ $video->has_custom }}}</td>
						<td>{{ link_to_route('teacher.videos.show', 'Preview', array($video->id), array('class' => 'btn btn-primary')) }}</td>
	                    <td>{{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}</td>
	                    <td>
	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id)) }}
	                        {{ Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $video->id)) }}
	                    {{ Form::close() }}
	                </td>
					</tr>
				@endforeach
			@else
				<tr><td colspan="4">No Videos Added</td></tr>
			@endif
		</tbody>
	</table>


<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@stop
