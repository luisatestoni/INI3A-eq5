<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Publicacao;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeralNotification;
use Illuminate\Support\Str;

class ComentarioController extends Controller
{
    public function salvar(Request $request)
    {
        $request->validate([
            'id_publicacao' => 'required',
            'conteudo'      => 'required|string|max:1000',
            'id_pai'        => 'nullable|integer',
        ]);

        // Garante que se id_pai vier vazio, seja gravado como NULL no banco
        $idPai = $request->filled('id_pai') ? $request->id_pai : null;

        $comentario = Comentario::create([
            'fk_id_post'    => $request->id_publicacao,
            'fk_id_usuario' => Auth::id(),
            'conteudo'      => $request->conteudo,
            'id_pai'        => $idPai,
        ]);

        $post = Publicacao::find($request->id_publicacao);

        // NOTIFICAÇÕES
        if ($idPai) {
            $comentarioPai = Comentario::with('usuario')->find($idPai);
            if ($comentarioPai && $comentarioPai->fk_id_usuario !== Auth::id() && $comentarioPai->usuario) {
                $comentarioPai->usuario->notify(new GeralNotification(
                    Auth::user(),
                    'resposta_comentario',
                    'respondeu ao seu comentário em: "' . Str::limit($post->titulo ?? '', 35) . '"',
                    route('publicacao.detalhes', $request->id_publicacao),
                    $post->titulo ?? ''
                ));
            }
        } else {
            if ($post && $post->fk_id_usuario !== Auth::id() && $post->usuario) {
                $post->usuario->notify(new GeralNotification(
                    Auth::user(),
                    'comentario',
                    'comentou em sua publicação: "' . Str::limit($post->titulo, 35) . '"',
                    route('publicacao.detalhes', $post->id_publicacao),
                    $post->titulo
                ));
            }
        }

        // Resposta AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $usuario = Auth::user();
            
            $fotoPerfil = ($usuario->perfil && $usuario->perfil->foto) 
                ? asset('storage/' . $usuario->perfil->foto) 
                : asset('imagens/perfil-v1.png');

            return response()->json([
                'sucesso'            => true,
                'id_comentario'      => $comentario->id_comentario,
                'id_pai'             => $comentario->id_pai,
                'conteudo'           => e($comentario->conteudo),
                'usuario_nome'       => $usuario->nome_usuario,
                'usuario_foto'       => $fotoPerfil,
                'usuario_perfil_url' => route('perfil.exibir', $usuario->id_usuario),
                'data_formatada'     => 'Agora mesmo'
            ]);
        }

        return redirect()->back()->with('sucesso', 'Resposta publicada!');
    }
}