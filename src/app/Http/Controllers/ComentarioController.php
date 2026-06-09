<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function salvar(Request $request)
    {
        $request->validate([
            'id_publicacao' => 'required',
            'conteudo' => 'required|string|max:1000',
        ]);

        Comentario::create([
            'fk_id_post' => $request->id_publicacao, // Bate com a sua migration!
            'fk_id_usuario' => Auth::id(),
            'conteudo' => $request->conteudo,
            'id_pai' => null, // Deixamos nulo para comentários principais
        ]);

        return redirect()->back()->with('sucesso', 'Comentário publicado!');
    }
}