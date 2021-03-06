<?php

/**
 * Based on:
 * Simple Ajax Uploader
 * Version 1.10.1
 * https://github.com/LPology/Simple-Ajax-Uploader
 *
 * Laravel Modifications made by rex.schrader@gmail.com
 *
 * Copyright 2012-2013 LPology, LLC
 * Released under the MIT license
 *
 * Returns upload progress updates for browsers that don't support the HTML5 File API.
 * Falling back to this method allows for upload progress support across virtually all browsers.
 *
 */

class UploadController extends BaseController {

	public function index($video_id)
	{
		Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		Breadcrumbs::addCrumb('Show Video', $video_id);
		Breadcrumbs::addCrumb('Upload Files');
		View::share('title', 'Upload Files');

		$filetype_list = Filetype::all();

		foreach($filetype_list as $filetype) {
			$filetypes[$filetype->name][] = $filetype->ext;
		}

		$ext_list = json_encode(array_values($filetype_list->lists('ext')));

		if(Video::find($video_id)) {
			return View::make('uploader.index', compact('video_id', 'filetypes', 'ext_list'));
		} else {
			return Redirect::route('teacher.videos.index')->with('message', 'Video not found');
		}
	}

	public function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	public function progress()
	{
		// This "if" statement is only necessary for CORS uploads -- if you're
		// only doing same-domain uploads then you can delete it if you want
		if (isset($_SERVER['HTTP_ORIGIN'])) {
		  header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		  header('Access-Control-Allow-Credentials: true');
		  header('Access-Control-Max-Age: 86400');    // cache for 1 day
		}

		if (isset($_REQUEST['progresskey']))
		  $status = apc_fetch('upload_'.$_REQUEST['progresskey']);
		else
		  exit(json_encode(array('success' => false)));

		$pct = 0;
		$size = 0;

		if (is_array($status)) {
		  if (array_key_exists('total', $status) && array_key_exists('current', $status)) {
		    if ($status['total'] > 0) {
		      $pct = round( ( $status['current'] / $status['total']) * 100 );
		      $size = round($status['total'] / 1024);
		    }
		  }
		}

		return json_encode(array('success' => true, 'pct' => $pct, 'size' => $size));
	}

	public function handler($video_id)
	{
		$base_dir = public_path() . '/uploads/';
		$file_dir = $base_dir . 'video_' . $video_id;

		$valid_ext = Filetype::lists('ext');

		// get upload file
		$Upload = new Uploader\FileUpload('uploadfile');
		$Upload->sizeLimit = 500000000;

		// make directory for uploads
		if(!is_dir($file_dir)) {
			mkdir($file_dir, 0774);
			if (!is_dir($file_dir)) {
				echo json_encode(array('success' => '1', 'msg' => 'Could not create upload directory'));
			}
		}

		// upload file
		$result = $Upload->handleUpload($file_dir, $valid_ext);

		// if failed, return
		if (!$result) {
			return json_encode(array('success' => '2', 'msg' => $Upload->getErrorMsg()));
		}
		// else, process uploaded file
		else {
			$path = $Upload->getSavedFile();
			$file = $Upload->getFileName();
			$ext = $Upload->getExtension();

			// file upload checks based upon filetype
			$filetype = Filetype::where('ext', $ext)->first();

			if(isset($filetype)) {
				$type = $filetype->type;
				if ( $type == 'video' ) {
//					$mimetype = explode('/', mime_content_type($path));
//					if ($mimetype[0] !== 'video' ) {
//						return json_encode(array('success' => '3', 'msg' => 'Invalid Video'));
//					}
					if (filesize($path) <= 10000000) {
						return json_encode(array('success' => '3', 'msg' => 'File Too Small.  Videos must be at least 15MB.'));
					}
				}
			} else {
			 	$type = 'other';
			}

			Files::firstOrCreate( [ 'video_id' => $video_id,
									'filetype_id' => isset($filetype->id) ? $filetype->id: 0,
									'filename' => $file,
									'desc' => '' ] );

			if($filetype->type == "video") {
				$video = Video::find($video_id);
				$video->has_vid = true;
				$video->save();
			}

			if($filetype->type == "code") {
				$video = Video::find($video_id);
				$video->has_code = true;
				$video->save();
			}

			// send response
			return json_encode(array('success' => '0', 'msg' => 'Success', 'file' => $file));
		}
	}

	public function delete_file($video_id, $file_id)
	{
	    // Make sure they have permission to delete_file
	    $video = Video::findorfail($video_id);
	    if(!(Roles::isAdmin() OR $video->teacher_id == Auth::user()->ID)) {
	        return Redirect::to(URL::previous())->with('error', 'You do not have permission to delete this file.');;
	    }

		$file = Files::find($file_id);
		if($file) {
			$file->delete();
		}
		return Redirect::to(URL::previous());
	}

	public function rename_file($video_id, $file_id)
	{
	    // Make sure they have permission to rename
	    $video = Video::findorfail($video_id);
	    if(!(Roles::isAdmin() OR $video->teacher_id == Auth::user()->ID)) {
	        return Response::json( ['success' => 0, 'msg' => 'Error: You do not have permission to rename file'] );
	    }

	    $filename = trim(Input::get('filename'));

	     // Validate the filename
	    if(preg_match('/[^a-zA-Z0-9_. -]/', $filename) OR empty($filename)) {
	        return Response::json( ['success' => 0, 'msg' => 'Error: Invalid Characters in filename'] );
	    }

		$file = Files::find($file_id);
		if($file) {
			if($file->rename($filename)) {
			    return Response::json( ['success' => 1,
			                         'msg' => 'Success',
			                         'filename' => $file->filename ] );
			} else {
			    return Response::json( ['success' => 0, 'msg' => 'Error: Renaming Error'] );
			}
		}
		return Response::json( ['success' => 0, 'msg' => 'Error: Cannot Find File'] );
	}
}
