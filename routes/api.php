<?php

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

Route::middleware('auth:api')->put('/perfil', function (Request $request) {
  $user = $request->user();
  $data = $request->all();

  $user->name = $data['name'];
  $user->email = $data['email'];

  if(isset($data['password'])){
    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
      'password' => 'required|string|min:6|confirmed',
    ]);
    
    $user->password = bcrypt($data['password']);

  }else{

    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => ['required','string','email','max:255',Rule::unique('users')->ignore($user->id)],
    ]);

  }

  if(isset($data['imagem'])){
    $time = time();
    $diretorioPai = 'perfils';
    $diretoriImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;
    $ext = substr($data['imagem'], 11,strpos($data['imagem'], ';')-11);
    $urlImagem = $diretoriImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext; 

    $file = str_replace('data:image/'.$ext.';base64,', '', $data['imagem']);//data:image/jpeg;base64,
    $file = base64_decode($file);

    if(!file_exists($diretorioPai)){
      mkdir($diretorioPai,0700);
    }
    if(!file_exists($diretoriImagem)){
      mkdir($diretoriImagem,0700);
    }

    file_put_contents($urlImagem, $file);

    $user->imagem = $urlImagem;

  }

  if($validator->fails()){
    return $validator->errors();
  }

  $user->save();

  $user->imagem = asset($user->imagem);
  $user->token = $user->createToken($user->email)->accessToken;
  return $user;
});
