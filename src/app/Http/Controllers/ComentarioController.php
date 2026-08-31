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

        $comentario = Comentario::create([
            'fk_id_post' => $request->id_publicacao, // Bate com a sua migration!
            'fk_id_usuario' => Auth::id(),
            'conteudo' => $request->conteudo,
            'id_pai' => null, // Deixamos nulo para comentários principais
        ]);

        // Caso a requisição venha do JavaScript (AJAX / Fetch)
        if ($request->ajax() || $request->wantsJson()) {
            $usuario = Auth::user();
            
            $fotoPerfil = ($usuario->perfil && $usuario->perfil->foto) 
                ? asset('storage/' . $usuario->perfil->foto) 
                : asset('imagens/perfil-v1.png');

            return response()->json([
                'sucesso' => true,
                'conteudo' => e($comentario->conteudo),
                'usuario_nome' => $usuario->nome_usuario,
                'usuario_foto' => $fotoPerfil,
                'usuario_perfil_url' => route('perfil.exibir', $usuario->id_usuario),
            ]);
        }

        // Mantém o comportamento normal caso o JavaScript falhe ou o envio venha sem AJAX
        return redirect()->back()->with('sucesso', 'Comentário publicado!');
    }
}