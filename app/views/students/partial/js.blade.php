@section('script')
var savedIndex = 0;

function setup_delete_buttons() {
	// Setup Delete Student buttons
	$('.remove_student').off('click').click(function() {
		var index = parseInt($(this).attr('index'),10);
		$('.student_' + index).remove();
	});
}


$(function() {
	// Initial setup for delete buttons
	setup_delete_buttons();

	// Setup Add Student button
	$('#add_student').click(function() {
		$.get( '/scoreboard/ajax/blank_student/' + savedIndex, function(data) {
			$('#student_form').append(data);
			setup_delete_buttons();
			savedIndex++;
		});
	});

	// Choose Students Dialog
	$('#choose_students_dialog').dialog({
		autoOpen: false,
		width: 500,
		open: function() {
			$(this).css({ "max-height", $(window).height(), 'overflow-y': 'auto'});
		},
		buttons: {
			"Add Students": function() {
				var data = $( "#student_list" ).serialize();
				$.post( '/scoreboard/ajax/load_students/' + savedIndex, data, function(returnData) {
					$('#student_form').append(returnData);
					setup_delete_buttons();
				});
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	// Show Choose Students Dialog
	$('#choose_students').click( function() {
		$('#choose_students_dialog').html('');
		var data = $('.student_id').map(function() { return $(this).attr('value'); }).get();
		var encoded_data = data.join('&');
@if(isset($use_teacher_id))
		// Using Teacher ID
		if($('#teacher_id').val() > 0 ) {
			$.post('/scoreboard/ajax/student_list/{{ $type }}/' + $('#teacher_id').val(), { current_students: data }, function(data) {
				$('#choose_students_dialog').html(data);
				$('#choose_students_dialog').dialog('open');
			});
		} else {
			alert('You must select a teacher first.');
		}
@else
		// Not Using Teacher ID
		$.post('/scoreboard/ajax/student_list/{{ $type }}', { current_students: data }, function(data) {
			$('#choose_students_dialog').html(data);
			$('#choose_students_dialog').dialog('open');
		});
@endif
	});

	// Mass Upload Students dialog
	$('#mass_upload_students_dialog').dialog({
		autoOpen: false,
		width: 500,
		buttons: {
			"Upload Students": function() {
				var data = { index: savedIndex };
				$('#upload_form').ajaxSubmit({data: data,
					success: function(responseText, statusText, xhr, $form) {
						$('#student_form').append(responseText);
						setup_delete_buttons();
					}});
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	// Handle Upload Button Click
	$('#mass_upload_students').click( function() {
		$('#mass_upload_students_dialog').dialog('open');
	});

});
@stop