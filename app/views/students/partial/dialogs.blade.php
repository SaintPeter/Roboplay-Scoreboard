{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

<div id="choose_students_dialog" title="Choose Students">

</div>

<div id="mass_upload_students_dialog" title="Choose Upload File">
Download this <a href="/scoreboard/docs/roboplay_scoreboard_student_upload_template.xls">Excel Template</a> and follow the instructions inside to generate a csv file for upload.
  {{ Form::open([ 'route' => 'ajax.import_students_csv', 'files' => true, 'id' => 'upload_form' ] ) }}
	  {{ Form::label('csv_file', 'CSV File', array('id'=>'','class'=>'')) }}
	  {{ Form::file('csv_file') }}
  <br/>
  <!-- submit buttons -->
  {{ Form::close() }}
</div>