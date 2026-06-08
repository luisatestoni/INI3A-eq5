<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguidor extends Model
{
    protected $table = 'seguidores';

    protected $primaryKey = 'id_seguidor';

    protected $fillable = [
        'fk_id_seguidor',
        'fk_id_seguido',
    ];

    public function seguidor()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_seguidor', 'id_usuario');
    }

    public function seguido()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_seguido', 'id_usuario');
    }
}