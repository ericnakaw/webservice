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
  }
