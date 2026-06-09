<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $primaryKey = 'id_comentario';

    protected $fillable = [
        'fk_id_post',
        'fk_id_usuario', // Note que na sua migration o campo se chama 'fk_id_usuario'
        'conteudo',
        'id_pai'
    ];

    /**
     * Relacionamento: Um comentário pertence a uma Publicação
     */
    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'fk_id_post', 'id_publicacao');
    }

    /**
     * ADICIONE ESTE MÉTODO: Um comentário pertence a um Usuário (Autor)
     */
    public function usuario()
    {
        // Vincula a coluna fk_id_usuario ao id_usuario da tabela de usuários
        return $this->belongsTo(Usuario::class, 'fk_id_usuario', 'id_usuario');
    }
}