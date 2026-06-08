<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nome',
        'nome_usuario',
        'email',
        'senha',
        'status',
    ];

    protected $hidden = [
        'senha',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function perfil()
    {
        return $this->hasOne(Perfil::class, 'fk_id_usuario', 'id_usuario');
    }

    public function publicacoes()
    {
        return $this->hasMany(Publicacao::class, 'fk_id_usuario', 'id_usuario');
    }

    public function seguidores()
    {
        return $this->hasMany(Seguidor::class, 'fk_id_seguido', 'id_usuario');
    }

    public function seguindo()
    {
        return $this->hasMany(Seguidor::class, 'fk_id_seguidor', 'id_usuario');
    }
}