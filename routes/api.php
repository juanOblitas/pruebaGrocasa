<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//@Todo: Auth
/*
Route::group(['middleware' =>  ['jwt.auth'], 'prefix' => 'v1'],function(){
	Route::post('/candidate/save','App\Http\Controllers\CandidateController@saveCandidate');
	Route::get('/candidate/all','App\Http\Controllers\CandidateController@showAllCandidates');
});*/

Route::group(['middleware' => ['jwt.auth'], 'prefix' => 'v1'], function () {

    Route::post('/candidate/save','App\Http\Controllers\CandidateController@saveCandidate');
	Route::get('/candidates/show','App\Http\Controllers\CandidateController@showCandidates');
	Route::get('/candidate/{candidate}','App\Http\Controllers\CandidateController@showCandidate');
});

Route::group(['middleware' => [], 'prefix' => 'v1'], function () {

    // Auth
    Route::post('/auth/login', 'App\Http\Controllers\TokensController@login');
    Route::post('/auth/refresh', 'App\Http\Controllers\TokensController@refreshToken');
    Route::get('/auth/logout', 'App\Http\Controllers\TokensController@logout');


});