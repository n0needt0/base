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

Route::group(array('domain' => 'base.helppain.net','domain' => 'base.this.com'), function()
{


    Route::get('/', 'HomeController@showWelcome');

    Route::controller('users', 'UserController');

    Route::controller('verify', 'VerifyController');

    Route::resource('groups', 'GroupController');

    Route::controller('emd', 'EmdController');

});

Route::group(array('domain' => 'search.helppain.net'), function()
{
        Route::get('/', 'SearchController@showWelcome');

});