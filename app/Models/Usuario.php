<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nome', 'usuario', 'email', 'senha', 'data_nascimento', 'status'
    ];

    protected $hidden = [
        'senha', 'remember_token',
    ];

    // O Laravel precisa saber onde está o campo de senha se mudarmos o nome padrão
    public function getAuthPassword() {
        return $this->senha;
    }

    public function perfil() {
        return $this->hasOne(Perfil::class, 'id_usuario', 'id_usuario');
    }

    public function posts() {
        return $this->hasMany(Post::class, 'id_usuario', 'id_usuario');
    }
}

