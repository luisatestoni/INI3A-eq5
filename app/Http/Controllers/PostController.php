<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function listarFeed(Request $request) {
        $abaAtiva = $request->get('aba', 'para-voce');

        $listaPosts = Post::with('usuario')
            ->where('status', 'publicado')
            ->latest()
            ->paginate(10);

        return view('feed', compact('listaPosts', 'abaAtiva'));
    }

    public function criarPublicacao() {
        return view('posts.create');
    }

    public function salvarPublicacao(Request $request) {
        $request->validate([
            'titulo' => 'required|max:100',
            'resumo' => 'nullable|max:200',
            'conteudo' => 'required',
            'podcast' => 'nullable|mimes:mp3,wav|max:20480',
            'capa' => 'nullable|image|max:2048',
        ]);

        $publicacao = new Post();
        $publicacao->id_usuario = auth()->user()->id_usuario;
        $publicacao->titulo = $request->titulo;
        $publicacao->resumo = $request->resumo;
        $publicacao->conteudo = $request->conteudo;
        $publicacao->status = 'publicado';

        if ($request->hasFile('capa')) {
            $publicacao->capa = $request->file('capa')->store('capas', 'public');
        }

        if ($request->hasFile('podcast')) {
            $publicacao->podcast_audio = $request->file('podcast')->store('podcasts', 'public');
        }

        $publicacao->save();

        return redirect()->route('feed')->with('sucesso', 'Publicado com sucesso!');
    }

    public function exibirPost($id) {
        $publicacao = Post::with('usuario')->findOrFail($id);
        return view('posts.show', compact('publicacao'));
    }
}
