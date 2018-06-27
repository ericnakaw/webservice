<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;

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

Route::post('/cadastro',function(Request $request) {
	$data = $request->all();

	$validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
  ]);

  if($validator->fails()){
  	return $validator->errors();
  }

	$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => bcrypt($data['password']),
	]);

	$user->token = $user->createToken($user->email)->accessToken;

	return $user;
});

Route::post('/login',function(Request $request) {
	$data = $request->all();

	$validator = Validator::make($data, [
      'email' => 'required|string|email|max:255',
      'password' => 'required|string',
  ]);

  if($validator->fails()){
  	return $validator->errors();
  }

  if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
  	$user = auth()->user();
		$user->token = $user->createToken($user->email)->accessToken;
		return $user;
  }else{
  	return [
  		'status' => false
  	];
  }
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
