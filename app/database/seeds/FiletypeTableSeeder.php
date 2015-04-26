<?php

class FiletypeTableSeeder extends Seeder {

	public function run()
	{
	Filetype::truncate();
	Filetype::create([ 'ext' => 'avi',	'type' => 'video',	'name' => 'Video File',	'language' => '',	'icon' => 'fa-file-video-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'mov',	'type' => 'video',	'name' => 'Video File',	'language' => '',	'icon' => 'fa-file-video-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'mp4',	'type' => 'video',	'name' => 'Video File',	'language' => '',	'icon' => 'fa-file-video-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'mkv',	'type' => 'video',	'name' => 'Video File',	'language' => '',	'icon' => 'fa-file-video-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'c',	'type' => 'code',	'name' => 'Source Code',	'language' => 'c',	'icon' => 'fa-file-code-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'ch',	'type' => 'code',	'name' => 'Source Code',	'language' => 'c',	'icon' => 'fa-file-code-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'cpp',	'type' => 'code',	'name' => 'Source Code',	'language' => 'cpp','icon' => 'fa-file-code-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'dwg',	'type' => 'cad',	'name' => 'CAD File',	'language' => '',	'icon' => 'fa-file-picture-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'jpg',	'type' => 'img',	'name' => 'Image File',	'language' => '',	'icon' => 'fa-file-picture-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'jpeg',	'type' => 'img',	'name' => 'Image File',	'language' => '',	'icon' => 'fa-file-picture-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'gif',	'type' => 'img',	'name' => 'Image File',	'language' => '',	'icon' => 'fa-file-picture-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'png',	'type' => 'img',	'name' => 'Image File',	'language' => '',	'icon' => 'fa-file-picture-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'doc',	'type' => 'doc',	'name' => 'Document',	'language' => '',	'icon' => 'fa-file-word-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'docx',	'type' => 'doc',	'name' => 'Document',	'language' => '',	'icon' => 'fa-file-word-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'pdf',	'type' => 'doc',	'name' => 'Document',	'language' => '',	'icon' => 'fa-file-pdf-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' => 'txt',	'type' => 'doc',	'name' => 'Document',	'language' => '',	'icon' => 'fa-file-text-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' =>  'wmv', 'type' =>  'video', 'name' =>  'Video File', 'language' => '',	'icon' => 'fa-file-video-o',	'viewer' => 'lytebox' ]);
	Filetype::create([ 'ext' =>  'asm', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'prt', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'step', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'stp', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'dxf', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'igs', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'stl', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'acis', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'catpart', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'ipt', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	Filetype::create([ 'ext' =>  'iam', 'type' =>  'cad', 'name' =>  'CAD File', 'language' => '' ]);
	}

}