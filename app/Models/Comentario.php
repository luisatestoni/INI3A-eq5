<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comentario';

    protected $fillable = [
        'id_post', 'id_usuario', 'conteudo', 'id_pai'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Auto-referência para respostas (sub-comentários)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'id_pai');
    }
}
?>