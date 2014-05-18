<h4>Files ({{ count($video->files) }})</h4>
<div style="width: 100%; height: 390px; margin: 0; overflow: auto; border: 1px solid black;">
	<table class="table">
		@if(count($video->files))
			@foreach($video->files as $file)
			<tr>
				@if($show_type) <td>{{ $file->filetype->name }}</td> @endif
				@if($file->filetype->type == 'code' OR $file->filetype->type == 'img' OR ($file->filetype->type == 'doc' and ($file->filetype->ext == 'txt' or $file->filetype->ext == 'pdf')))
					<td>{{ link_to($file->url(), $file->filename, [ 'target' => '_blank', 'class' => 'lytebox', 'data-title' => $file->filename, "data-lyte-options" => "group:group1" ]) }}</td>
					<td><a href="{{ url($file->path()) }}" target="_blank"><span class="glyphicon glyphicon-download" title="Download File"></span></a></td>
				@else
					<td>{{ link_to($file->path(), $file->filename, [ 'target' => '_blank' ]) }}</td>
					<td>&nbsp;</td>
				@endif
				@if($show_delete)
					<td><a href="{{ route('uploader.delete_file', [ 'video_id' => $video->id, 'file_id' => $file->id ]) }}"
					><span class="glyphicon glyphicon-remove" style="color: red;" title="Delete File"></span> </a></td>
				@endif
			</tr>
			@endforeach
		@else
			<tr><td>No Files</td></tr>
		@endif
	</table>
</div>