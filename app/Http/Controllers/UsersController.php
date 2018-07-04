<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function login(Request $request)
    {
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
        $user->imagem = asset($user->imagem);
        $user->token = $user->createToken($user->email)->accessToken;
        return $user;
      }else{
        return [
          'status' => false
        ];
      }
    }

    public function cadastro(Request $request)
    {
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
    }

    public function user(Request $request)
    {
      return $request->user();
    }

    public function perfil(Request $request)
    {
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
        //perfils/perfil_id7
        $diretoriImagem = $diretorioPai.DIRECTORY_SEPARATOR.'perfil_id'.$user->id;
        $ext = substr($data['imagem'], 11,strpos($data['imagem'], ';')-11);
        //perfils/perfil_id7/123456.jpg
        $urlImagem = $diretoriImagem.DIRECTORY_SEPARATOR.$time.'.'.$ext;
        //data:image/jpeg;base64,
        $file = str_replace('data:image/'.$ext.';base64,', '', $data['imagem']);
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
    }

  }
