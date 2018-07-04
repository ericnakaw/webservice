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

  $imagem = '/perfils/default.png';

  $user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => bcrypt($data['password']),
    'imagem' => $imagem,
  ]);

  $user->imagem = asset($user->imagem);
  $user->token = $user->createToken($user->email)->accessToken;

  return $user;
});

Route::post('/login','UsersController@login');

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:api')->put('/perfil', function (Request $request) {
  $user = $request->user();
  $data = $request->all();

  $user->name = $data['name'];
  $user->email = $data['email'];
  $user->descricao = $data['descricao'];

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

    Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
      $explode = explode(',', $value);
      $allow = ['png', 'jpg', 'jpeg', 'svg'];
      $format = str_replace(
        [
          'data:image/',
          ';',
          'base64',
        ],
        [
          '', '', '',
        ],
        $explode[0]
      );
        // check file format
      if (!in_array($format, $allow)) {
        return false;
      }
        // check base64 format
      if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
        return false;
      }
      return true;
    });

    $validator = Validator::make($data, [
      'imagem' => 'base64image'
    ],['base64image'=>'Imagem inválida']);

    if($validator->fails()){
      return $validator->errors();
    }

    $time = time();
    $diretorioPai = 'perfils';
    $diretoriImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;//perfils/perfil_id7
    $ext = substr($data['imagem'], 11,strpos($data['imagem'], ';')-11);
    $urlImagem = $diretoriImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;//perfils/perfil_id7/123456.jpg

    $file = str_replace('data:image/'.$ext.';base64,', '', $data['imagem']);//data:image/jpeg;base64,
    $file = base64_decode($file);

    if(!file_exists($diretorioPai)){
      mkdir($diretorioPai,0700);
    }

    if($user->imagem){
      if(file_exists($user->imagem)){
        unlink($user->imagem);
      }
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
