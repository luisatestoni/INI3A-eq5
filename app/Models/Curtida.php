<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curtida extends Model
{
    protected $table = 'curtidas';

    protected $primaryKey = 'id_curtida';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_usuario',
        'fk_id_publicacao',
        'data_curtida'
    ];

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'fk_id_usuario',
            'id_usuario'
        );
    }

    public function publicacao()
    {
        return $this->belongsTo(
            Publicacao::class,
            'fk_id_publicacao',
            'id_publicacao'
        );
    }
}