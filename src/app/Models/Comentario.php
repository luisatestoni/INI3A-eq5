<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    // Define a chave primária personalizada
    protected $primaryKey = 'id_comentario';

    protected $fillable = ['fk_id_post', 'fk_id_usuario', 'conteudo', 'id_pai'];

    public function respostas()
    {
        return $this->hasMany(Comentario::class, 'id_pai', 'id_comentario');
    }

    public function pai()
    {
        return $this->belongsTo(Comentario::class, 'id_pai', 'id_comentario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_usuario', 'id_usuario');
    }
}