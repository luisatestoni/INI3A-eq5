<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Nome da tabela que criamos na migration
    protected $table = 'categorias';

    // Campos que o Laravel tem permissão para preencher em massa (massa assignment)
    protected $fillable = [
        'nome',
        'grupo'
    ];
}