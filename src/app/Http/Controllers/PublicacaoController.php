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
    public function listarFeed()
    {
        $publicacoes = Publicacao::with(['usuario.perfil'])
            ->orderBy('data_publicacao', 'desc')
            ->get();

        return view('feed', compact('publicacoes'));
    }

    public function criar()
    {
        $categorias = Categoria::all()->groupBy('grupo');

        return view('publicacoes.criar', compact('categorias'));
    }

    public function salvar(Request $requisicao)
    {
        // Removidos os ini_set() ineficazes daqui

        $requisicao->validate([
            'titulo' => 'required|max:255',
            'resumo' => 'nullable|max:500',
            'conteudo' => 'required',
            'capa' => 'nullable|image|max:10240', // Aumentado para 10MB
            'podcast' => 'nullable|file|extensions:mp3,wav,ogg,m4a,aac,oga,mp4,webm|max:102400',            'categorias' => 'required|string'
        ]);

        $publicacao = new Publicacao();
        $publicacao->fk_id_usuario = Auth::guard('web')->id();
        $publicacao->titulo = $requisicao->titulo;
        $publicacao->resumo = $requisicao->resumo;
        $publicacao->conteudo = $requisicao->conteudo;
        $publicacao->categorias = $requisicao->categorias;

        if ($requisicao->hasFile('capa')) {
            $caminhoCapa = $requisicao->file('capa')->store('capas_posts', 'public');
            $publicacao->capa = $caminhoCapa;
        }

        if ($requisicao->hasFile('podcast')) {
            $caminhoAudio = $requisicao->file('podcast')->store('podcasts', 'public');
            $publicacao->podcast = $caminhoAudio; 
        }

        $publicacao->save();

        return redirect()->route('feed')->with('sucesso', 'Publicação criada com sucesso!');
    }

    public function detalhes($id_publicacao)
    {
        // Busca o post trazendo junto o usuário, perfil, curtidas e comentários
        $post = \App\Models\Publicacao::with(['usuario.perfil', 'curtidas', 'comentarios.usuario.perfil'])
                ->findOrFail($id_publicacao);

        // Retorna a view correta de detalhes
        return view('publicacoes.detalhes', compact('post'));
    }

    // Método para abrir o formulário de edição
    public function editar($id)
    {
        $post = Publicacao::findOrFail($id);

        // Trava de segurança no backend
        if (!Auth::check() || Auth::id() !== $post->fk_id_usuario) {
            abort(403, 'Você não tem permissão para editar esta publicação.');
        }

        // BUSCA AS CATEGORIAS (Ajuste a lógica abaixo para bater com como você faz no método criar)
        // Se o seu dropdown usa grupos (como parece no @foreach), você provavelmente usa algo assim:
        $categorias = Categoria::all()->groupBy('grupo'); 
        
        // Se você não usa grupos no banco e apenas traz todas, pode ser apenas:
        // $categorias = Categoria::all();

        // Envia o $post E as $categorias para a mesma view
        return view('publicacoes.criar', compact('post', 'categorias'));
    }

    public function atualizar(Request $request, $id)
    {
        $post = Publicacao::findOrFail($id);

        if (Auth::id() !== $post->fk_id_usuario) {
            abort(403);
        }

        // Adicionado as mesmas validações de arquivo na edição para manter consistência
        $request->validate([
            'titulo' => 'required|max:255',
            'resumo' => 'nullable|max:500',
            'conteudo' => 'required',
            'capa' => 'nullable|image|max:10240',
            'podcast' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,oga|max:102400',
            'categorias' => 'required|string'
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

        return gi()->with('sucesso', 'Publicação atualizada com sucesso!');
    }

    public function deletar($id)
    {
        // 1. Captura a página de onde o usuário veio ANTES de disparar a exclusão
        // Se por algum motivo falhar, o padrão (fallback) será o feed
        $urlAnterior = url()->previous();

        // Busca a publicação ou retorna 404 se não existir
        $post = Publicacao::findOrFail($id);

        // Segurança máxima: Garante que apenas o dono do post pode deletar
        if (!Auth::check() || Auth::id() !== $post->fk_id_usuario) {
            abort(403, 'Você não tem permissão para excluir esta publicação.');
        }

        // Se o post tiver uma imagem de capa cadastrada, apaga o arquivo do storage
        if ($post->capa) {
            Storage::disk('public')->delete($post->capa);
        }

        // Se o post tiver um podcast gravado, apaga o arquivo de áudio do storage
        if ($post->podcast) {
            Storage::disk('public')->delete($post->podcast);
        }

        // Deleta o registro do banco de dados
        $post->delete();

        // 2. A MÁGICA: Se a URL anterior continha a própria página de detalhes, 
        // significa que o usuário clicou em deletar de dentro dos detalhes.
        // Nesse caso, o url()->previous() falharia (daria 404). Então checamos isso:
        if (str_contains($urlAnterior, "/publicacao/{$id}/detalhes")) {
            // Se ele deletou de dentro dos detalhes, não podemos usar o previous literal.
            // O Laravel guarda o histórico real no "session". Vamos redirecionar para o feed como porto seguro,
            // ou você pode forçar para o perfil do usuário já que ele acabou de apagar o próprio post:
            return redirect()->route('perfil.exibir', Auth::id())->with('sucesso', 'Publicação excluída com sucesso!');
        }

        // Se ele deletou direto de um botão no Feed ou no Perfil (sem entrar nos detalhes), 
        // aí sim o redirect para a URL anterior funciona perfeitamente!
        return redirect()->to($urlAnterior)->with('sucesso', 'Publicação excluída com sucesso!');
    }

    public function curtir($id_publicacao)
    {
        $id_usuario = Auth::id();
        
        if (!$id_usuario) {
            return response()->json(['erro' => 'Não autorizado'], 401);
        }

        $curtidaExistente = \App\Models\Curtida::where('fk_id_usuario', $id_usuario)
                                            ->where('fk_id_publicacao', $id_publicacao)
                                            ->first();

        if ($curtidaExistente) {
            $curtidaExistente->delete();
            $curtido = false;
        } else {
            $novaCurtida = new \App\Models\Curtida();
            $novaCurtida->fk_id_usuario = $id_usuario;
            $novaCurtida->fk_id_publicacao = $id_publicacao;
            $novaCurtida->save();
            $curtido = true;
        }

        // Conta quantas curtidas o post tem no total agora
        $totalCurtidas = \App\Models\Curtida::where('fk_id_publicacao', $id_publicacao)->count();

        // RETORNO CORRIGIDO: Atende perfeitamente ao "data.sucesso" do detalhes.js
        return response()->json([
            'sucesso' => true,
            'curtido' => $curtido,
            'total_curtidas' => $totalCurtidas
        ]);
    }
     // --- AJUSTADO EXATAMENTE PARA O SEU "data.status === 'sucesso'" ---

    public function comentar(Request $requisicao)

    {

        $requisicao->validate([

            'id_publicacao' => 'required|integer|exists:publicacoes,id_publicacao',

            'conteudo' => 'required|string|max:1000',

            'fk_id_comentario_pai' => 'nullable|integer|exists:comentarios,id_comentario'

        ]);


        $id_usuario = Auth::id();


        $comentario = new \App\Models\Comentario();

        $comentario->fk_id_usuario = $id_usuario;

        $comentario->fk_id_publicacao = $requisicao->id_publicacao;

        $comentario->conteudo = $requisicao->conteudo;

       

        if ($requisicao->has('fk_id_comentario_pai')) {

            $comentario->fk_id_comentario_pai = $requisicao->fk_id_comentario_pai;

        }

       

        $comentario->save();



        // O seu JS sempre envia cabeçalho de requisição AJAX, então ele cai direto aqui

        if ($requisicao->ajax() || $requisicao->wantsJson()) {

            return response()->json([

                'status' => 'sucesso', // Atende a linha "if (data.status === 'sucesso')" do seu detalhes.js

                'mensagem' => 'Postado com sucesso!'

            ]);

        }



        // Fallback caso alguém envie sem JavaScript ativado no navegador

        return back();

    }
}
