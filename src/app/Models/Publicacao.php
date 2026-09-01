<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Certifique-se de que essas linhas existem:
use App\Models\Usuario;
use App\Models\Curtida;
use App\Models\Comentario; 
use App\Models\Perfil;


class Publicacao extends Model
{
    protected $table = 'publicacoes';
    protected $primaryKey = 'id_publicacao';
    const CREATED_AT = 'data_publicacao';
    const UPDATED_AT = 'data_atualizacao';

   protected $fillable = [
        'fk_id_usuario',
        'titulo',
        'resumo',
        'conteudo',
        'categorias',
        'status',
        'capa',
        'podcast',
        'data_publicacao',
        'data_atualizacao',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_usuario', 'id_usuario');
    }

    public function curtidas()
    {
        // Na migration de curtidas está: fk_id_publicacao (Correto!)
        return $this->hasMany(Curtida::class, 'fk_id_publicacao', 'id_publicacao');
    }

    public function comentarios()
    {
        // CORREÇÃO: Na sua migration de comentários está: fk_id_post
        return $this->hasMany(Comentario::class, 'fk_id_post', 'id_publicacao');
    }

    // app/Models/Publicacao.php
    public function salvos()
    {
        return $this->hasMany(Salvo::class, 'fk_id_publicacao');
    }
}