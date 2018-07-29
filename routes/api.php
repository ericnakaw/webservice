<?php

use App\User;
use App\Conteudo;
use App\Comentario;
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

Route::get('/testes',function(){
    $user = User::find(1);
    $user2 = User::find(3);
    /*
    $user->conteudos()->create([
        'titulo' => 'Conteudo 3',
        'texto' => 'Aqui tem um texto',
        'imagem' => 'url da imagem',
        'link' => 'Link',
        'data' => '2018-07-10',
    ]);
    return $user->conteudos;
    */
    
    /*
    //Add Amigos
    $user->amigos()->attach($user2->id);
    $user->amigos()->detach($user2->id);
    $user->amigos()->toggle($user2->id);
    */
    
    return $user->amigos;
});