<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Publicacao extends Model
{
    protected $table = 'publicacoes';
    protected $primaryKey = 'id_publicacao';
    const CREATED_AT = 'data_publicacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'fk_id_usuario', 'titulo', 'resumo', 'conteudo', 'capa', 'podcast', 'status', 'categorias'

    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_usuario', 'id_usuario');
    }

    public function curtidas()
    {
        return $this->hasMany(Curtida::class, 'fk_id_publicacao', 'id_publicacao');
    }

    public function comentarios()
    {
        return $this->hasMany(
            Comentario::class,
            'fk_id_publicacao',
            'id_publicacao'
        );
    }
}