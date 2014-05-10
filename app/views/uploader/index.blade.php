@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/SimpleAjaxUploader.min.js') }}
@stop

@section('script')
	$(function() {
		var user_id = 'test';
		var video_id = 'vid';
		var uploader = new ss.SimpleUpload({
			button: 'uploadButton',
			url: '{{ route('uploader.handler', [ $video_id ]) }}',
			progressUrl: '{{ route('uploader.progress') }}',
			responseType: 'json',
			name: 'uploadfile',
			multiple: true,
			maxUploads: 3,
			queue: true,
			hoverClass: 'ui-state-hover',
			focusClass: 'ui-state-focus',
			disabledClass: 'ui-state-disabled',
			onSizeError: function(filename, fileSize) {
				var output = document.getElementById('output');
				output.className = 'alert alert-warning';
				output.innerHTML = '<strong>Warning:</strong> File size exceeds upload limit.';
			},
			onSubmit: function(filename, extension) {
				// hide upload button
				//var noupload = document.getElementById('noupload');
				//noupload.style.display = 'none';

				// Create the elements of our progress bar
				var progress = document.createElement('div'),
					bar = document.createElement('div'),
					fileSize = document.createElement('div'),
					wrapper = document.createElement('div'),
					progressBox = document.getElementById('progressBox');

				// Assign each element its corresponding class
				progress.className = 'progress';
				bar.className = 'bar';
				fileSize.className = 'size';
				wrapper.className = 'wrapper';

				// Assemble the progress bar and add it to the page
				progress.appendChild(bar);
				wrapper.innerHTML = '<div class="name">'+filename+'</div>';
				wrapper.appendChild(fileSize);
				wrapper.appendChild(progress);
				progressBox.appendChild(wrapper);

				// Assign roles to the elements of the progress bar
				this.setProgressBar(bar);
				this.setFileSizeBox(fileSize);
				this.setProgressContainer(wrapper);
			},
			onComplete: function(filename, response) {
				if (!response || response.success != 0) {
					// unhide upload button
					//var noupload = document.getElementById('noupload');
					//noupload.style.display = 'inline-block';

					// set output message alert
					var output = document.getElementById('output');
					output.className = 'alert alert-danger';
					output.innerHTML = 'Upload failed: ' + response.msg;
					return false;
				}
				else {
					var output = document.getElementById('output');
					var message = document.createElement('div');
					message.className = 'alert alert-success';
					message.innerHTML = 'Thank you for uploading ' + response.file;
					output.appendChild(message);
					return true;
				}
			}
		});
	});
@stop

@section('main')
<h1>File Uploads</h1>
{{ Breadcrumbs::render() }}
<div class="col-md-6">
	<h4>Known File Types</h4>
	@foreach($filetypes as $type => $ext_list)
		<strong>{{ $type }}</strong>
		<p>{{ join(', ', $ext_list) }}</p>
	@endforeach
	<br />
	<div id="uploads">
		<div id="noupload">
			<input id="uploadButton" class="btn btn-primary btn-large" type="button" value="Choose File"></input>
		</div>
		<div id="progressBox"></div>
		<div id="output"></div>
	</div>
	<div style="display: block; position: absolute; overflow: hidden; margin: 0px; padding: 0px; opacity: 0; direction: ltr; z-index: 2147483583; left: 413px; top: 192px; width: 100px; height: 34px; visibility: hidden;">
		<input style="position: absolute; right: 0px; margin: 0px; padding: 0px; font-size: 480px; font-family: sans-serif; cursor: pointer;" accept="image/*" multiple="" name="imgfile" type="file">
	</div>
	<br />
	<br />
	{{ link_to_route('teacher.videos.show', 'Return to Video', $video_id, [ 'class' => 'btn btn-info' ]) }}
</div>
@stop
