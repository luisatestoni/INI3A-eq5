<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfil';
    protected $primaryKey = 'id_perfil';

    protected $fillable = [
        'id_usuario', 'bio', 'foto', 'tipo', 'site'
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}