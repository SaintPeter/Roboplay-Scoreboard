<h4>Files ({{ count($video->files) }})</h4>
<div style="width: 100%; height: 480px; margin: 0; overflow: auto; border: 1px solid black;">
	<table class="table">
		@if(count($video->files))
			@foreach($video->files as $file)
			<tr>
				@if($show_type) <td>{{ $file->filetype->name }}</td> @endif
				@if($file->filetype->viewer == 'lytebox')
					<td>
						<a href="{{ $file->url() }}" class="lytebox" data-title="{{ $file->filename }}" data-lyte-options="group:group1" target="_blank" >
							<i class="fa {{ $file->filetype->icon}}"></i>
							{{ $file->filename }}
						</a>
					</td>
					<td><a href="{{ url($file->path()) }}" target="_blank"><span class="glyphicon glyphicon-download" title="Download File"></span></a></td>
				@else
					<td>
						<a href="{{ url($file->path()) }}" target="_blank">
							<i class="fa {{ $file->filetype->icon}}"></i>
							{{ $file->filename }}
						</a>
					</td>
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