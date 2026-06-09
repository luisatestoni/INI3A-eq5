<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curtida extends Model
{
    protected $table = 'curtidas';
    protected $primaryKey = 'id_curtida';

    protected $fillable = [
        'fk_id_usuario',
        'fk_id_publicacao'
    ];

    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'fk_id_publicacao', 'id_publicacao');
    }
}