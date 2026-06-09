<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; // IMPORTANTE: Nova interface

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nome', 'nome_usuario', 'email', 'senha', 'status'
    ];

    protected $hidden = [
        'senha',
    ];

    // --- MÉTODOS OBRIGATÓRIOS DO JWT ---
    
    // Identificador único guardado no Subject (sub) do Token
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Retorna o id_usuario
    }

    // Custom Claims: Dados não sensíveis descritos no seu relatório
    public function getJWTCustomClaims()
    {
        return [
            'nome' => $this->nome,
            'nome_usuario' => $this->nome_usuario
        ];
    }

    // Mapeia o campo correto de senha para o Auth do Laravel
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

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'fk_id_usuario', 'id_usuario');
    }

    // Dentro de app/Models/Usuario.php

// Usuários que SEGUEM este usuário (Seguidores)
    public function seguidores()
    {
        // Parâmetros: (Model Relacionado, Tabela Pivot, Chave FK de quem é seguido, Chave FK de quem segue)
        return $this->belongsToMany(
            Usuario::class, 
            'seguidores', 
            'fk_id_seguido', 
            'fk_id_seguidor'
        );
    }

    // Usuários que este usuário ESTÁ SEGUINDO (Seguindo)
    public function seguindo()
    {
        // Parâmetros: (Model Relacionado, Tabela Pivot, Chave FK de quem segue, Chave FK de quem é seguido)
        return $this->belongsToMany(
            Usuario::class, 
            'seguidores', 
            'fk_id_seguidor', 
            'fk_id_seguido'
        );
    }
}