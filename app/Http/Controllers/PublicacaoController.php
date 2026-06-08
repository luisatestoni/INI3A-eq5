<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Curtida;
use App\Models\Publicacao;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicacaoController extends BaseController
{
    public function listarFeed(Request $request)
    {
        $abaAtiva = $request->query('aba', 'para-voce');

        $listaPosts = Publicacao::with('usuario.perfil')
            ->orderBy('data_publicacao', 'desc')
            ->get();

        return view('feed', compact('listaPosts', 'abaAtiva'));
    }

    public function criar()
    {
        $categorias = Categoria::all()->groupBy('grupo');

        return view('publicacoes.criar', compact('categorias'));
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'capa' => 'nullable|image|max:10240',
            'podcast' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,oga,mp4,webm|max:102400',
            'categorias' => 'nullable|string',
        ]);

        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $publicacao = new Publicacao();
        $publicacao->fk_id_usuario = $usuario->id_usuario;
        $publicacao->titulo = $request->titulo;
        $publicacao->resumo = $request->resumo;
        $publicacao->conteudo = $request->conteudo;
        $publicacao->categorias = $request->categorias;
        $publicacao->status = 'publicado';

        if ($request->hasFile('capa')) {
            $publicacao->capa = $request->file('capa')->store('capas_posts', 'public');
        }

        if ($request->hasFile('podcast')) {
            $publicacao->podcast = $request->file('podcast')->store('podcasts', 'public');
        }

        $publicacao->save();

        return redirect()
            ->route('feed')
            ->with('sucesso', 'Publicação criada com sucesso!');
    }

    public function detalhes($id)
    {
        $post = Publicacao::with([
            'usuario.perfil',
            'curtidas',
            'comentarios.usuario.perfil'
        ])->findOrFail($id);

        return view('publicacoes.detalhes', compact('post'));
    }

    public function editar($id)
    {
        $post = Publicacao::findOrFail($id);

        if (!Auth::check() || Auth::id() !== $post->fk_id_usuario) {
            abort(403, 'Você não tem permissão para editar esta publicação.');
        }

        $categorias = Categoria::all()->groupBy('grupo');

        return view('publicacoes.criar', compact('post', 'categorias'));
    }

    public function atualizar(Request $request, $id)
    {
        $post = Publicacao::findOrFail($id);

        if (!Auth::check() || Auth::id() !== $post->fk_id_usuario) {
            abort(403, 'Você não tem permissão para editar esta publicação.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'capa' => 'nullable|image|max:10240',
            'podcast' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,oga,mp4,webm|max:102400',
            'categorias' => 'nullable|string',
        ]);

        $post->titulo = $request->titulo;
        $post->resumo = $request->resumo;
        $post->conteudo = $request->conteudo;
        $post->categorias = $request->categorias;

        if ($request->hasFile('capa')) {
            if ($post->capa) {
                Storage::disk('public')->delete($post->capa);
            }

            $post->capa = $request->file('capa')->store('capas_posts', 'public');
        }

        if ($request->hasFile('podcast')) {
            if ($post->podcast) {
                Storage::disk('public')->delete($post->podcast);
            }

            $post->podcast = $request->file('podcast')->store('podcasts', 'public');
        }

        $post->save();

        return redirect()
            ->route('publicacao.detalhes', $post->id_publicacao)
            ->with('sucesso', 'Publicação atualizada com sucesso!');
    }

    public function deletar($id)
    {
        $post = Publicacao::findOrFail($id);

        if (!Auth::check() || Auth::id() !== $post->fk_id_usuario) {
            abort(403, 'Você não tem permissão para excluir esta publicação.');
        }

        if ($post->capa) {
            Storage::disk('public')->delete($post->capa);
        }

        if ($post->podcast) {
            Storage::disk('public')->delete($post->podcast);
        }

        $post->delete();

        return redirect()
            ->route('feed')
            ->with('sucesso', 'Publicação excluída com sucesso!');
    }

    public function curtir($id)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return response()->json(['erro' => 'Não autorizado'], 401);
        }

        $curtida = Curtida::where('fk_id_usuario', $usuario->id_usuario)
            ->where('fk_id_publicacao', $id)
            ->first();

        if ($curtida) {
            $curtida->delete();
        } else {
            Curtida::create([
                'fk_id_usuario' => $usuario->id_usuario,
                'fk_id_publicacao' => $id,
            ]);
        }

        return response()->json([
            'status' => 'sucesso',
        ]);
    }

    public function comentar(Request $request)
    {
        $request->validate([
            'id_publicacao' => 'required|integer|exists:publicacoes,id_publicacao',
            'conteudo' => 'required|string|max:1000',
            'fk_id_comentario_pai' => 'nullable|integer',
        ]);

        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        DB::table('comentarios')->insert([
            'fk_id_usuario' => $usuario->id_usuario,
            'fk_id_post' => $request->id_publicacao,
            'conteudo' => $request->conteudo,
            'id_pai' => $request->fk_id_comentario_pai,
            'data_comentario' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'sucesso',
                'mensagem' => 'Comentário publicado com sucesso!',
            ]);
        }

        return back()->with('sucesso', 'Comentário publicado com sucesso!');
    }
}