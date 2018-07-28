<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','imagem','descricao'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function comentarios()
    {
        return $this->hasMany('App\Comentario');
    }
    
    public function conteudos()
    {
        return $this->hasMany('App\Conteudo');
    }
    
    public function curtidas()
    {
        return $this->belongsToMany('App\Conteudo', 'curtidas', 'user_id', 'conteudo_id');
    }
    
    public function amigos()
    {
        return $this->belongsToMany('App\User', 'amigos', 'user_id', 'amigo_id');
    }
}
