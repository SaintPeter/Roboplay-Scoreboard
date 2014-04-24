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

Route::get('/', [ 'as' => 'home', function()
{
	$competitions = Competition::all();
	$noajax = array('data-ajax' => "false");
	return View::make('home', compact('competitions', 'noajax'));
}]);

Route::get('team/{team_id}', array('as' => 'display.teamscore', 'uses' => 'DisplayController@teamscore'))
		 ->where('team_id', '\d+');
Route::get('comp/{competition_id}', array('as' => 'display.compscore', 'uses' => 'DisplayController@compscore'))
		 ->where('competition_id', '\d+');
		 
/* ------------------------- Ajax Handlers -------------------------- */
Route::get('ajax/d/{county_id}', [ 'as' => 'ajax.districts', 'uses' => 'Wp_fix@ajax_districts']);
Route::get('ajax/s/{district_id}', [ 'as' => 'ajax.schools', 'uses' => 'Wp_fix@ajax_schools']);


/* ------------------------------ User ------------------------------------- */
// All items in this route group require login
Route::group(array('before' => 'auth'), function() {

	Route::get('testlogin', function() {
		return "Logged in! - " . Auth::user()->user_email;
	});

	// Logout Page
	Route::get('logout', function() {
		Auth::logout();
		return Redirect::to('/');
	});

	Route::group(array('before' => 'admin'), function () {
		Route::resource('competitions', 'CompetitionsController');
		Route::resource('challenges', 'ChallengesController');
		Route::resource('divisions', 'DivisionsController');
		Route::get('divisions/assign/{division_id}', array(
				   'as' => 'divisions.assign',
				   'uses' => 'DivisionsController@assign'));
		Route::post('divisions/saveassign', array(
				   'as' => 'divisions.saveassign',
				   'uses' => 'DivisionsController@saveassign'));
		Route::post('divisions/removeChallenge/{division_id}/{challenge_id}', array(
				   'as' => 'divisions.removeChallenge',
				   'uses' => 'DivisionsController@removeChallenge'));
		Route::post('divisions/updateChallengeOrder/{division_id}', array(
				   'as' => 'divisions.updateChallengeOrder',
				   'uses' => 'DivisionsController@updateChallengeOrder'));
		Route::resource('vid_competitions', 'Vid_competitionsController');
		Route::resource('vid_divisions', 'Vid_divisionsController');
		Route::resource('teams', 'TeamsController');
		Route::resource('score_elements', 'Score_elementsController');
		Route::get('score_elements/{challenge_id}/create', array(
				   'as' => 'score_elements.create',
				   'uses' => 'Score_elementsController@create'));
		Route::resource('score_runs', 'Score_runsController');
		Route::resource('judges', 'JudgesController');
		Route::resource('videos', 'VideosController');
		
		Route::get('user_schools', [ 'as' => 'user_schools', 'uses' => 'Wp_fix@user_schools']);
	});

	Route::group(array('before' => 'judge'), function () {

		Route::get('score', array(
				   'as' => 'score.choose_competition',
				   'uses' =>'ScoreController@index'));
		Route::get('score/c/{competition_id}', array(
				   'as' => 'score.choose_division',
				   'uses' =>'ScoreController@index'))
				   ->where('competition_id', '\d+');
		Route::get('score/c/{competition_id}/d/{division_id}', array(
				   'as' => 'score.choose_team',
				   'uses' =>'ScoreController@index'))
				   ->where('competition_id', '\d+')
				   ->where('division_id', '\d+');
		Route::get('score/c/{competition_id}/d/{division_id}/t/{team_id}', array(
				   'as' => 'score.score_team',
				   'uses' =>'ScoreController@index'))
				   ->where('competition_id', '\d+')
				   ->where('division_id', '\d+')
				   ->where('team_id', '\d+');
		Route::get('score/{team_id}/{challenge_id}', array(
				   'as' => 'score.doscore',
				   'uses' =>'ScoreController@doscore'))
				   ->where('team_id', '\d+')
				   ->where('challenge_id', '\d+');
		Route::post('score/save/{team_id}/{challenge_id}', array(
				   'as' => 'score.save',
				   'uses' =>'ScoreController@save'));
		Route::post('score/preview/{team_id}/{challenge_id}', array(
				   'as' => 'score.preview',
				   'uses' =>'ScoreController@preview'));
		Route::get('score/competition/{competition_id}', array(
				   'as' => 'score.competition',
				   'uses' =>'ScoreController@competition'));
		Route::get('score/division/{division_id}', array(
				   'as' => 'score.division',
				   'uses' =>'ScoreController@division'));
		Route::get('score/team/{team_id}', array(
				   'as' => 'score.team',
				   'uses' =>'ScoreController@team'));
	});

	Route::group(array('before' => 'teacher'), function ()
	{
		Route::resource('teacher/teams', 'TeacherTeamsController');
		Route::resource('teacher/videos', 'TeacherVideoController');
	});

});


// Basic Login Page
Route::get('login', function()
{
	if(Auth::check()) {
		return Redirect::to('/');
	} else {
		return View::make('login');
	}
});

// Post Login Page
Route::post('login', function() {
	// get POST data
	$userdata = array(
	    'user_login' => Input::get('username'),
	    'user_pass' => Input::get('password')
	);

	if (Auth::attempt($userdata))
	{
		// Update Judge Info
		Judge::do_sync();

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




