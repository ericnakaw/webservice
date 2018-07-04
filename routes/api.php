<?php

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

Route::post('/cadastro','UsersController@cadastro');
Route::post('/login','UsersController@login');
Route::middleware('auth:api')->get('/user','UsersController@user');
Route::middleware('auth:api')->put('/perfil','UsersController@perfil');
