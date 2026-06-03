<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id_post';

    protected $fillable = [
        'id_usuario', 'titulo', 'resumo', 'conteudo', 'podcast_audio', 'capa', 'status'
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}