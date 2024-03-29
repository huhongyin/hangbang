<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', 'Web\AirController@index');

Route::get('/add/{id?}', 'Web\AirController@add');

Route::get('/list', 'Web\AirController@getList');

Route::post('/doAdd', 'Web\AirController@doAdd');

Route::delete('/delete/{id}', 'Web\AirController@delete');