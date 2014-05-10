<?php

class FiletypeTableSeeder extends Seeder {

	public function run()
	{
	Filetype::truncate();
	Filetype::create([ 'ext' => 'avi',	'type' => 'video',	'name' => 'Video File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'mov',	'type' => 'video',	'name' => 'Video File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'mp4',	'type' => 'video',	'name' => 'Video File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'mkv',	'type' => 'video',	'name' => 'Video File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'c',	'type' => 'code',	'name' => 'Source Code',	'language' => 'c' ]);
	Filetype::create([ 'ext' => 'ch',	'type' => 'code',	'name' => 'Source Code',	'language' => 'c' ]);
	Filetype::create([ 'ext' => 'cpp',	'type' => 'code',	'name' => 'Source Code',	'language' => 'cpp' ]);
	Filetype::create([ 'ext' => 'dwg',	'type' => 'cad',	'name' => 'CAD File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'jpg',	'type' => 'img',	'name' => 'Image File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'jpeg',	'type' => 'img',	'name' => 'Image File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'gif',	'type' => 'img',	'name' => 'Image File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'png',	'type' => 'img',	'name' => 'Image File',	'language' => '' ]);
	Filetype::create([ 'ext' => 'doc',	'type' => 'doc',	'name' => 'Document',	'language' => '' ]);
	Filetype::create([ 'ext' => 'docx',	'type' => 'doc',	'name' => 'Document',	'language' => '' ]);
	Filetype::create([ 'ext' => 'pdf',	'type' => 'doc',	'name' => 'Document',	'language' => '' ]);
	Filetype::create([ 'ext' => 'txt',	'type' => 'doc',	'name' => 'Document',	'language' => '' ]);

	}

}