<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salvo extends Model
{
    protected $table = 'salvos';
    protected $primaryKey = 'id_salvo';
    protected $fillable = ['fk_id_usuario', 'fk_id_publicacao'];

    public function publicacao()
    {
        return $this->belongsTo(Publicacao::class, 'fk_id_publicacao');
    }
}