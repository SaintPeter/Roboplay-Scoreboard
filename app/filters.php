<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	// Store changes to selected year
	if(Input::has('year')) {
		$year = Input::get('year');
		if($year == '') {
			Session::forget('year');
		} else {
			Session::put('year', $year);
		}
	}

	// Store changes to selected level
	if(Input::has('level_select')) {
		$level_select = Input::get('level_select');
		if($level_select == 0) {
			Session::forget('level_select');
		} else {
			Session::put('level_select', $level_select);
		}
	}

});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});

Route::filter('admin', function()
{
	if (Auth::guest()) {
		return Redirect::guest('login');
	} else {
		if(!Roles::isAdmin()) {
			return "You do not have permission to admin.";
		}
	}
});

Route::filter('judge', function()
{
	if (Auth::guest()) {
		return Redirect::guest('login');
	} else {
		if(!Roles::isJudge()) {
			return "You do not have permission to Judge";
		}
	}
});

Route::filter('teacher', function()
{
	if (Auth::guest()) {
		return Redirect::guest('login');
	} else {
		if(!Roles::isTeacher()) {
			return "You are not a Teacher";
		}
	}
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});