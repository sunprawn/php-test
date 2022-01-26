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

Route::get('/', 'ApiController@index');
Route::get('search/{search}', 'ApiController@search');
Route::get('students/{id}', 'ApiController@student');
Route::get('courses/{id}', 'ApiController@course');
//Route::get('enrollment/{search}', 'ApiController@enrollment');

