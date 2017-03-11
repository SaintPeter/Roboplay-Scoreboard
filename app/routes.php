<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('file_viewer/{file_id}', [ 'as' => 'file_viewer', function($file_id) {
	$file = Files::find($file_id);
	if(file_exists($file->full_path())) {
		$source = file_get_contents($file->full_path());
		$geshi = new Geshi\Geshi($source, $file->filetype->language);
		$geshi->set_overall_style('white-space: pre-wrap;', true); // Force the pre tag to wrap in the container (iframe)
		echo $geshi->parse_code();
	} else {
		echo "ERROR: File '{$file->filename}' not found.";
	}
}]);


Route::get('/', [ 'as' => 'home', function()
{
	$today = Carbon\Carbon::now()->setTimezone('America/Los_Angeles')->startOfDay();

	$compyears = CompYear::orderBy('year', 'desc')
	                     ->with('competitions', 'competitions.divisions',
	                            'vid_competitions','math_competitions')
	                     ->get();
	$noajax = array('data-ajax' => "false");
	return View::make('home', compact('compyears', 'noajax', 'today'));
}]);

/* ----------------------- Score Display ---------------------------- */
// Team Scores
Route::get('team/{team_id}', array('as' => 'display.teamscore', 'uses' => 'DisplayController@teamscore'))
		 ->where('team_id', '\d+');
Route::get('team/{team_id}/{with_judges}', array('as' => 'display.teamscore', 'uses' => 'DisplayController@teamscore'))
		 ->where('team_id', '\d+');

// Single Competiton
Route::get('comp/{competition_id}/{csv?}', array('as' => 'display.compscore', 'uses' => 'DisplayController@compscore'))
		 ->where('competition_id', '\d+')
		 ->where('csv','csv');
Route::get('comp/top/{competition_id}/{csv?}', array('as' => 'display.compscore.top', 'uses' => 'DisplayController@compscore_top'))
		 ->where('competition_id', '\d+')
		 ->where('csv','csv');

// Entire Competition Year
Route::get('compyear/{compyear_id}/{csv?}', array('as' => 'display.compyearscore', 'uses' => 'DisplayController@compyearscore'))
		 ->where('compyear_id', '\d+')
		 ->where('csv','csv');
Route::get('compyear/top/{compyear_id}/{csv?}', array('as' => 'display.compyearscore.top', 'uses' => 'DisplayController@compyearscore_top'))
		 ->where('compyear_id', '\d+')
		 ->where('csv','csv');

// Attempts
Route::get('attempts/{compyear_id}', array('as' => 'display.attempts', 'uses' => 'DisplayController@attempts'));

// All Scores
Route::get('all_scores/{compyear_id}', array('as' => 'display.all_scores', 'uses' => 'DisplayController@all_scores'))
		 ->where('compyear_id', '\d+');

// Paging Settings
Route::post('comp/{competition_id}/settings', [ 'as' => 'display.compsettings', 'uses' => 'DisplayController@compsettings' ])
		->where('competition_id', '\d+');
Route::post('compyear/{compyear_id}/settings', [ 'as' => 'display.compyearsettings', 'uses' => 'DisplayController@compyearsettings' ])
		->where('compyear_id', '\d+');
Route::post('all_scores/{compyear_id}/settings', [ 'as' => 'display.all_scores_settings', 'uses' => 'DisplayController@all_scores_settings' ])
		->where('compyear_id', '\d+');
Route::post('attempts/{compyear_id}/settings', [ 'as' => 'display.attempts_settings', 'uses' => 'DisplayController@attempts_settings' ])
		->where('compyear_id', '\d+');


Route::get('export_scores/{year}', [ 'as' => 'display.export_scores', 'uses' => 'DisplayController@export_year_scores']);

/* ----------------------- Video Display ---------------------------- */
Route::get('video_list/{comp_id}/{video_id}', [ 'as' => 'display.show_video', 'uses' => 'DisplayController@show_video'] )
        ->where('video_id', '\d+');
Route::get('video_list/{comp_id}/{winners?}', [ 'as' => 'display.video_list', 'uses' => 'DisplayController@video_list'] );

/* ------------------------- Ajax Handlers -------------------------- */
Route::get('ajax/c', 				[ 'as' => 'ajax.counties', 	'uses' => 'Wp_fix@ajax_counties']);
Route::get('ajax/d/{county_id}', 	[ 'as' => 'ajax.districts', 'uses' => 'Wp_fix@ajax_districts']);
Route::get('ajax/s/{district_id}', 	[ 'as' => 'ajax.schools', 	'uses' => 'Wp_fix@ajax_schools']);
Route::get('ajax/blank_student/{index}', [ 'as' => 'ajax.blank_student', 'uses' => 'TeacherController@ajax_blank_student' ]);
Route::post('ajax/student_list/{type}/{teacher_id?}',	[ 'as' => 'ajax.student_list',  		'uses' => 'TeacherController@ajax_student_list' ]);
Route::post('ajax/load_students/{index}',  				[ 'as' => 'ajax.load_students',  		'uses' => 'TeacherController@ajax_load_students' ]);
Route::post('ajax/import_students_csv',  				[ 'as' => 'ajax.import_students_csv',	'uses' => 'TeacherController@ajax_import_students_csv' ]);

/* ------------------------------ User ------------------------------------- */
// All items in this route group require login
Route::group(array('before' => 'auth'), function() {

	Route::get('testlogin', function() {
		return "Logged in! - " . Auth::user()->user_email;
	});

	// Logout Page
	Route::get('logout', [ 'as' => 'logout', function() {
		Auth::logout();
		return Redirect::to('/');
	}]);

/* ----------------------- Admin Routes --------------------------------------*/
	Route::group(array('before' => 'admin'), function () {
		// Export Functions
		Route::get('challenge_students_csv', [ 'uses' => 'DisplayController@challenge_students_csv' ] );
		Route::get('video_students_csv', [ 'uses' => 'DisplayController@video_students_csv' ] );

		// Manage CompYears
		Route::resource('compyears', 'CompYearsController');

		// Manage Competitions
		Route::resource('competitions', 'CompetitionsController');
		Route::get('competitions/toggle_frozen/{competition_id}', [ 'as' => 'competition.toggle_frozen', 'uses' => 'CompetitionsController@toggle_frozen' ]);
		Route::get('competitions/freeze/all', [ 'as' => 'competitions.freeze.all', 'uses' => 'CompetitionsController@freeze_all' ]);
		Route::get('competitions/unfreeze/all', [ 'as' => 'competitions.unfreeze.all', 'uses' => 'CompetitionsController@unfreeze_all' ]);
		Route::get('competitions/toggle_active/{competition_id}', [ 'as' => 'competition.toggle_active', 'uses' => 'CompetitionsController@toggle_active' ]);

		// Manage Math Competitions
		Route::resource('math_competitions', 'MathCompetitionsController');
		Route::resource('math_challenges', 'MathChallengesController');
		Route::resource('math_divisions', 'MathDivisionsController');
		Route::resource('math_teams', 'MathTeamsController');
		Route::get('math_challenges/{division_id}/create', [ 'as' => 'math_challenges.create',	'uses' => 'MathChallengesController@create']);


		// Display Competition Scores Unfrozen
//		Route::get('comp/{competition_id}/{do_not_freeze}', array('as' => 'display.compscore.do_not_freeze', 'uses' => 'DisplayController@compscore'))
//		 ->where('competition_id', '\d+');

		// Manage Challenges
		Route::resource('challenges', 'ChallengesController');
		Route::get('challenges/{id}/duplicate', [ 'as' => 'challenges.duplicate', 'uses' => 'ChallengesController@duplicate' ]);

		// Manage Divisions
		Route::resource('divisions', 'DivisionsController');
		Route::get('divisions/assign/{division_id}', array(
				   'as' => 'divisions.assign',
				   'uses' => 'DivisionsController@assign'));
		Route::get('divisions/{division_id}/clear_scores', array(
				   'as' => 'divisions.clear_scores',
				   'uses' => 'DivisionsController@clear_scores'))
				   ->where('division_id', '\d+');
		Route::get('divisions/clear/all_scores', array(
				   'as' => 'divisions.clear_all_scores',
				   'uses' => 'DivisionsController@clear_all_scores'));
		Route::post('divisions/saveassign', array(
				   'as' => 'divisions.saveassign',
				   'uses' => 'DivisionsController@saveassign'));
		Route::get('divisions/{division_id}/remove/{challenge_id}', array(
				   'as' => 'divisions.removeChallenge',
				   'uses' => 'DivisionsController@removeChallenge'));
		Route::post('divisions/copy/{from_id}', [ 'as' => 'divisions.copyChallenges', 'uses' => 'DivisionsController@copyChallenges' ] );
		Route::get('divisions/clear/{division_id}', [ 'as' => 'divisions.clearChallenges', 'uses' => 'DivisionsController@clearChallenges' ] );
		Route::post('divisions/updateChallengeOrder/{division_id}', array(
				   'as' => 'divisions.updateChallengeOrder',
				   'uses' => 'DivisionsController@updateChallengeOrder'));

		//  Manage Video Competitions
		Route::resource('vid_competitions', 'Vid_competitionsController');
		Route::resource('vid_divisions', 'Vid_divisionsController');

		Route::resource('teams', 'TeamsController');
		Route::resource('score_elements', 'Score_elementsController');
		Route::get('score_elements/{challenge_id}/create', [ 'as' => 'score_elements.create',	'uses' => 'Score_elementsController@create']);
		Route::resource('randoms', 'RandomsController');
		Route::get('randoms/{challenge_id}/create', 	   [ 'as' => 'randoms.create',			'uses' => 'RandomsController@create']);
		Route::resource('random_list', 'RandomListsController');
		Route::get('random_list/{challenge_id}/create', 	[ 'as' => 'random_list.create',			'uses' => 'RandomListsController@create']);
		Route::get('list_elements/{random_list_id}/edit', 	[ 'as' => 'list_elements.edit',	'uses' => 'RandomListsController@edit_list_elements']);
		Route::post('list_elements/{random_list_id}/save', 	[ 'as' => 'list_elements.save',	'uses' => 'RandomListsController@save_list_elements']);
		Route::get('list_elements/{random_list_id}/show', 	[ 'as' => 'list_elements.show',	'uses' => 'RandomListsController@show_list_elements']);
		Route::resource('score_runs', 'Score_runsController');
		Route::resource('judges', 'JudgesController');
		Route::resource('videos', 'VideosController');

		// Magical WP Fix Stuff - Not for "production"
		Route::get('user_schools', 							[ 'as' => 'user_schools', 	'uses' => 'Wp_fix@user_schools']);
		Route::get('division_check', 						[ 'as' => 'division_check', 'uses' => 'Wp_fix@division_check']);
		Route::get('team_division_check', 					[ 'as' => 'team_division_check', 'uses' => 'Wp_fix@team_division_check']);
		Route::get('invoice_fix', 							[ 'as' => 'invoice_fix', 	'uses' => 'Wp_fix@invoice_fix']);
		Route::get('invoice_management',					[ 'as' => 'invoice_set', 	'uses' => 'Wp_fix@invoice_set']);
		Route::get('invoice_csv',							[ 'as' => 'invoice_csv', 	'uses' => 'Wp_fix@invoice_csv']);
		Route::get('student_fix_school',					[ 'as' => 'student_fix_school',  'uses' => 'Wp_fix@student_fix_school']);
		Route::get('switch_user/{user_id}',					[ 'as' => 'switch_user',  	'uses' => 'Wp_fix@switch_user']);
		Route::get('list_judges',							[ 'as' => 'list_judges', 	'uses' => 'Wp_fix@list_judges']);
		Route::get('ajax/set_paid/{invoice_no}/{value}', 	[ 'as' => 'ajax.set_paid', 	'uses' => 'Wp_fix@ajax_set_paid']);
		Route::get('ajax/set_div/{invoice_no}/{value}', 	[ 'as' => 'ajax.set_div', 	'uses' => 'Wp_fix@ajax_set_div']);
		Route::get('ajax/set_vid_div/{invoice_no}/{value}', [ 'as' => 'ajax.set_vid_div', 'uses' => 'Wp_fix@ajax_set_vid_div']);
		Route::post('ajax/save_school', 					[ 'as' => 'ajax.save_school', 'uses' => 'Wp_fix@ajax_save_school']);
		Route::get('student_list',							[ 'as' => 'student_list', 'uses' => 'Wp_fix@student_list'] );

		// Invoice Review
		Route::get('invoice_review/toggle_video/{id?}',     [ 'as' => 'invoice_review.toggle_video', 'uses' => 'InvoiceReview@toggle_video' ]);
		Route::get('invoice_review/toggle_team/{id?}',      [ 'as' => 'invoice_review.toggle_team',  'uses' => 'InvoiceReview@toggle_team' ]);
		Route::get('invoice_review/toggle_paid/{id?}',      [ 'as' => 'invoice_review.toggle_paid',  'uses' => 'InvoiceReview@toggle_paid' ]);
		Route::get('invoice_review/save_video_div/{video_id?}/{div_id?}',  [ 'as' => 'invoice_review.save_video_div', 'uses' => 'InvoiceReview@save_video_division' ]);
		Route::get('invoice_review/save_team_div/{team_id?}/{div_id?}',    [ 'as' => 'invoice_review.save_team_div', 'uses' => 'InvoiceReview@save_team_division' ]);
		Route::post('invoice_review/save_video_notes/{id?}',      [ 'as' => 'invoice_review.save_video_notes', 'uses' => 'InvoiceReview@save_video_notes' ]);
		Route::get('invoice_review/{year?}/{terse?}',     	[ 'as' => 'invoice_review',		         'uses' => 'InvoiceReview@invoice_review' ])
		     ->where('year', '\d{4}');
		Route::get('invoice_sync/{year}',					[ 'as' => 'invoice_sync',		         'uses' => 'InvoiceReview@invoice_sync' ]);

		Route::get('data_export/{year?}',					[ 'as' => 'data_export',    'uses' => 'InvoiceReview@data_export' ])
		     ->where('year', '\d{4}');
		Route::get('data_export/student_tshirts_{year}.csv',		[ 'as' => 'data_export.student_tshirts',    'uses' => 'InvoiceReview@student_tshirts_csv' ]);
		Route::get('data_export/teacher_tshirts_{year}.csv',		[ 'as' => 'data_export.teacher_tshirts',    'uses' => 'InvoiceReview@teacher_tshirts_csv' ]);
		Route::get('data_export/teacher_teams_{year}.csv',			[ 'as' => 'data_export.teacher_teams',    'uses' => 'InvoiceReview@teacher_teams_csv' ]);
		Route::get('data_export/student_demographics_{year}.csv',	[ 'as' => 'data_export.student_demographics',    'uses' => 'InvoiceReview@student_demographics_csv' ]);
		Route::get('data_export/video_demographics_{year}.csv',	    [ 'as' => 'data_export.video_demographics',    'uses' => 'InvoiceReview@video_demographics_csv' ]);

        // File Types Editing
        Route::resource('filetypes', 'FileTypesController');
        Route::get('filetypes/add/{type?}',          [ 'as' => 'filetypes.add',     'uses' => 'FileTypesController@create' ]);
        /*
        Route::post('filetypes/store',        [ 'as' => 'filetypes.store',     'uses' => 'FileTypesController@store' ]);
        Route::post('filetypes/update/{id}',  [ 'as' => 'filetypes.update',    'uses' => 'FileTypesController@update' ]);
        */

		// Video Scores management
		Route::get('video_scores/{year?}', [
				   'as' => 'video_scores.manage.index',
				   'uses' => 'VideoManagementController@index' ])
				   ->where('year', '\d{4}');
		Route::get('video_scores/reported/{year?}', [
				   'as' => 'video_scores.manage.reported',
				   'uses' => 'VideoManagementController@reported_videos' ])
				   ->where('year', '\d{4}');
		Route::get('video_scores/by_video/{year?}', [
				   'as' => 'video_scores.manage.by_video',
				   'uses' => 'VideoManagementController@by_video' ])
				   ->where('year', '\d{4}');
		Route::post('video_scores/process', [
				    'as' => 'video_scores.manage.process',
				    'uses' => 'VideoManagementController@process' ]);
		Route::post('video_scores/process_report', [
				    'as' => 'video_scores.manage.process_report',
				    'uses' => 'VideoManagementController@process_report' ]);
		Route::get('video_scores/unresolve/{comment_id}', [
				   'as' => 'video_scores.manage.unresolve',
				   'uses' => 'VideoManagementController@unresolve' ]);
		Route::get('video_scores/scores_csv/{year?}', [
				    'as' => 'video_scores.manage.scores_csv',
				    'uses' => 'VideoManagementController@scores_csv' ])
				    ->where('year', '\d{4}');
		Route::get('video_scores/summary/{year?}', [
				    'as' => 'video_scores.manage.summary',
				    'uses' => 'VideoManagementController@summary' ])
				    ->where('year', '\d{4}');
		Route::get('video_scores/judge_performance/{year?}', [
				    'as' => 'video_scores.manage.judge_performance',
				    'uses' => 'VideoManagementController@judge_performance' ])
				    ->where('year', '\d{4}');
	    Route::get('video_scores/graphs/{year?}', [
				    'as' => 'video_scores.manage.graphs',
				    'uses' => 'VideoManagementController@graphs' ])
				    ->where('year', '\d{4}');

        // Graph Generation Routines
	    Route::get('video_scores/video_performance_{year}.jpg', [
	                'as' => 'graph_video_performace',
	                'uses' => 'VideoManagementController@graph_video_scoring' ]);
	    Route::get('video_scores/judge_performance_{year}.jpg', [
	                'as' => 'graph_judge_performace',
	                'uses' => 'VideoManagementController@graph_judge_scoring' ]);

	    // Schedule Editing
	    Route::get('schedule', [ 'as' => 'schedule.index', 'uses' => 'ScheduleController@index' ]);
	    Route::post('schedule', [ 'as' => 'schedule.update', 'uses' => 'ScheduleController@update' ]);

	});

/* -----------------------------------------------------------------------------
|                              Judge Routes                                     |
------------------------------------------------------------------------------*/

	Route::group(array('before' => 'judge'), function () {
		// Delete individual score
		Route::get('team/{team_id}/delete_score/{score_run_id}', [
		   'as' => 'display.teamscore.delete_score',
		   'uses' => 'DisplayController@delete_score' ] )
		 ->where('team_id', '\d+');
		 Route::get('team/{team_id}/restore_score/{score_run_id}', [
		   'as' => 'display.teamscore.restore_score',
		   'uses' => 'DisplayController@restore_score' ] )
		 ->where('team_id', '\d+');

		 // Delete individual score
		Route::get('mathteam/{team_id}/delete_score/{score_run_id}', [
		   'as' => 'display.mathteamscore.delete_score',
		   'uses' => 'MathDisplayController@delete_score' ] )
		 ->where('team_id', '\d+');
		 Route::get(',athteam/{team_id}/restore_score/{score_run_id}', [
		   'as' => 'display.mathteamscore.restore_score',
		   'uses' => 'MathDisplayController@restore_score' ] )
		 ->where('team_id', '\d+');

		// Challenge Scoring
		Route::get('score', 									[ 'as' => 'score.choose_competition',	'uses' =>'ScoreController@index' ] );
		Route::get('score/c/{comp_id}', 						[ 'as' => 'score.choose_division',		'uses' =>'ScoreController@index' ] )
					->where('competition_id', '\d+');
		Route::get('score/c/{comp_id}/d/{div_id}',				[ 'as' => 'score.choose_team',			'uses' =>'ScoreController@index' ] )
					->where('competition_id', '\d+')
					->where('division_id', '\d+');
		Route::get('score/c/{comp_id}/d/{div_id}/t/{team_id}',	[ 'as' => 'score.score_team',			'uses' =>'ScoreController@index' ] )
				   ->where('comp_id', '\d+')
				   ->where('div_id', '\d+')
				   ->where('team_id', '\d+');
		Route::get('score/{team_id}/{challenge_id}', 			[ 'as' => 'score.doscore',	'uses' =>'ScoreController@doscore' ] )
				   ->where('team_id', '\d+')
				   ->where('challenge_id', '\d+');
		Route::post('score/save/{team_id}/{challenge_id}', 		[ 'as' => 'score.save',	'uses' =>'ScoreController@save' ] );

		// Math Challenge Scoring
		Route::get('math_score', 									[ 'as' => 'math_score.choose_competition',	'uses' =>'MathScoreController@index' ] );
		Route::get('math_score/c/{comp_id}', 						[ 'as' => 'math_score.choose_division',		'uses' =>'MathScoreController@index' ] )
					->where('competition_id', '\d+');
		Route::get('math_score/c/{comp_id}/d/{div_id}',				[ 'as' => 'math_score.choose_team',			'uses' =>'MathScoreController@index' ] )
					->where('competition_id', '\d+')
					->where('division_id', '\d+');
		Route::get('math_score/c/{comp_id}/d/{div_id}/t/{team_id}',	[ 'as' => 'math_score.score_team',			'uses' =>'MathScoreController@index' ] )
				   ->where('comp_id', '\d+')
				   ->where('div_id', '\d+')
				   ->where('team_id', '\d+');
		Route::get('math_score/{team_id}/{challenge_id}', 			[ 'as' => 'math_score.doscore',	'uses' =>'MathScoreController@doscore' ] )
				   ->where('team_id', '\d+')
				   ->where('challenge_id', '\d+');
		Route::post('math_score/save/{team_id}/{challenge_id}', 		[ 'as' => 'math_score.save',	'uses' =>'MathScoreController@save' ] );
		Route::get('math_score/{score_id}/edit',	[ 'as' => 'math_score.editscore',			'uses' =>'MathScoreController@editscore' ] )
				   ->where('score_id', '\d+');
		Route::post('math_score/update/{score_id}', 		[ 'as' => 'math_score.update',	'uses' =>'MathScoreController@update' ] );

		// Video Judging
		//Route::resource('video/judge', 'ScoreVideosController');
		Route::get('video/judge', [
					'as' => 'video.judge.index',
					'uses' => 'ScoreVideosController@index' ]);
		Route::get('video/judge/{video_id}/edit', [
					'as' => 'video.judge.edit',
					'uses' => 'ScoreVideosController@edit' ]);
		Route::get('video/judge/{video_id}/show', [
					'as' => 'video.judge.show',
					'uses' => 'ScoreVideosController@show' ]);
		Route::get('video/judge/dispatch', [
					'as' => 'video.judge.dispatch',
					'uses' => 'ScoreVideosController@dispatch' ]);
		Route::get('video/judge/score/{video_id}', [
					'as' => 'video.judge.score',
					'uses' => 'ScoreVideosController@score' ]);
		Route::post('video/judge/store/{video_id}', [
					'as' => 'video.judge.store',
					'uses' => 'ScoreVideosController@store' ]);
		Route::post('video/judge/update/{video_id}', [
					'as' => 'video.judge.update',
					'uses' => 'ScoreVideosController@update' ]);
		Route::get('video/judge/clear/{video_id}/{judge_id}', [
					'as' => 'video.judge.clear_scores',
					'uses' => 'ScoreVideosController@clear_scores' ]);
	});

/* ----------------------- Teacher Routes ----------------------------------*/
	Route::group(array('before' => 'teacher'), function ()
	{
		Route::resource('teacher/teams', 'TeacherTeamsController');
		Route::resource('teacher/videos', 'TeacherVideoController');
		Route::resource('teacher/math_teams', 'TeacherMathTeamsController');

		Route::get('teacher/video/{video_id}/delete/{file_id}', [
					'as' => 'uploader.delete_file',
					'uses' => 'UploadController@delete_file' ])
					->where('video_id', '\d+')
					->where('file_id', '\d+');

	    Route::post('teacher/video/{video_id}/rename/{file_id}', [
					'as' => 'uploader.rename_file',
					'uses' => 'UploadController@rename_file' ])
					->where('video_id', '\d+')
					->where('file_id', '\d+');

		Route::get('uploader/{video_id}', [
				   'as' => 'uploader.index',
				   'uses' => 'UploadController@index' ])
				   ->where('video_id', '\d+');
		Route::post('uploader/handler/{video_id}', [
				   'as' => 'uploader.handler',
				   'uses' => 'UploadController@handler' ])
				   ->where('video_id', '\d+');;
		Route::get('uploader/progress', [
				   'as' => 'uploader.progress',
				   'uses' => 'UploadController@progress' ]);

		// New Combined Teacher Controller
		Route::get('teacher', [
					'as' => 'teacher.index',
					'uses' => 'TeacherController@index' ]);

	    Route::post('teacher/save_tshirt', [ 'as' => 'teacher.save_tshirt', 'uses' => 'TeacherController@save_tshirt' ]);


		Route::resource('students', 'StudentsController');
	});

});


// Basic Login Page
Route::get('login', [ 'as' => 'login', function()
{
	if(Auth::check()) {
		return Redirect::to('/');
	} else {
		return View::make('login');
	}
}]);

// Post Login Page
Route::post('login', function() {
	// get POST data
	$userdata = array(
	    'user_login' => Input::get('username'),
	    'user_pass' => Input::get('password')
	);

	if (Auth::attempt($userdata, false))
	{
		// Update Judge Info
		Judge::do_sync();

		//ddd(Redirect::intended('/'));
	    // Go where we intended to go, or back to the home page
		return Redirect::intended('/');
	}
	else
	{
	    // authentication failed
		return Redirect::to('login')
			->with('login_errors', true);
	}
});




