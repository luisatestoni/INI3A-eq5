<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';
    protected $primaryKey = 'id_perfil';
    public $timestamps = false; // Usando data_atualizacao manual do banco se necessário

    protected $fillable = [
        'fk_id_usuario', 'bio', 'foto', 'tipo', 'data_atualizacao'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fk_id_usuario', 'id_usuario');
    }

    public function exibir($id)
    {
        $usuario = Usuario::with([
            'publicacoes.usuario.perfil',
            'salvos.publicacao.usuario.perfil',
            'curtidas.publicacao.usuario.perfil'
        ])->findOrFail($id);

        return view('perfil', compact('usuario'));
    }
}