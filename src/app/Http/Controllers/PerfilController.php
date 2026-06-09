<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\Seguidor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends BaseController
{
    public function exibir($id)
    {
        $usuario = Usuario::with([
            'perfil',
            'publicacoes' => function ($query) {
                $query->with(['usuario.perfil', 'curtidas', 'comentarios'])
                    ->orderBy('data_publicacao', 'desc');
            },
            'seguidores',
            'seguindo'
        ])->findOrFail($id);

        return view('perfil.exibir', compact('usuario'));
    }

    public function editar($id_usuario)
    {
        // SEGURANÇA MÁXIMA: Pega o ID de quem está logado pelo JWT do cookie
        $usuarioLogadoId = Auth::guard('web')->id();

        // Se o ID da URL for diferente do ID de quem está logado, barra na hora!
        if ($usuarioLogadoId != $id_usuario) {
            return redirect()->route('feed')->withErrors([
                'permissao' => 'Você não tem permissão para editar o perfil de outra pessoa!'
            ]);
        }

        $usuario = Usuario::with('perfil')->findOrFail($id_usuario);
        return view('perfil.editar', compact('usuario'));
    }

    public function atualizar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'nome_usuario' => 'required|string|max:255',
            'biografia' => 'nullable|string|max:500',
            'foto' => 'nullable|image|max:2048',
            'capa' => 'nullable|image|max:4096',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Usuario::findOrFail(Auth::id());

        $usuario->nome = $request->nome;
        $usuario->nome_usuario = $request->nome_usuario;
        $usuario->save();

        $perfil = Perfil::where('fk_id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            $perfil = new Perfil();
            $perfil->fk_id_usuario = $usuario->id_usuario;
            $perfil->tipo = 'comum';
        }

        $perfil->bio = $request->biografia;

        if ($request->hasFile('foto')) {
            if (!empty($perfil->foto)) {
                Storage::disk('public')->delete($perfil->foto);
            }

            $perfil->foto = $request->file('foto')->store('avatares', 'public');
        }

        if ($request->hasFile('capa')) {
            if (!empty($perfil->capa)) {
                Storage::disk('public')->delete($perfil->capa);
            }

            $perfil->capa = $request->file('capa')->store('capas_perfil', 'public');
        }

        $perfil->save();

        return redirect()
            ->route('perfil.exibir', ['id' => $usuario->id_usuario])
            ->with('sucesso', 'Perfil atualizado com sucesso!');
    }

    // Remove permanentemente o usuário e limpa o cookie JWT do navegador
    public function excluirConta()
    {
        $idUsuarioLogado = Auth::id(); // Pega o ID autenticado pelo JWT

        if (!$idUsuarioLogado) {
            return redirect()->route('login');
        }

        // Busca o usuário logado no banco de dados
        $usuario = Usuario::findOrFail($idUsuarioLogado);

        // Remove o usuário. Como configuramos onDelete('cascade') nas migrations,
        // o perfil e as publicações vinculadas serão limpos automaticamente.
        $usuario->delete();

        // Limpa o cookie JWT do navegador de forma definitiva
        $cookieLimpo = cookie()->forget('jwt_token');

        // Retorna o usuário expulso para a landing page sem rastros do login
        return redirect()->route('inicial')->withCookie($cookieLimpo)->with('sucesso', 'Sua conta foi excluída com sucesso.');
    }

    public function telaAlterarSenha()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('perfil.alterar-senha');
    }

    public function alterarSenha(Request $request)
    {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|string|min:6|confirmed',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Usuario::findOrFail(Auth::id());

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            return back()->withErrors([
                'senha_atual' => 'A senha atual está incorreta.',
            ]);
        }

        $usuario->senha = Hash::make($request->nova_senha);
        $usuario->save();

        return back()->with('sucesso', 'Senha alterada com sucesso!');
    }

    public function configuracoes()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Usuario::with('perfil')->findOrFail(Auth::id());

        return view('perfil.configuracoes', compact('usuario'));
    }

    public function listarSeguidores($id)
    {
        // 1. Busca o usuário dono do perfil que está sendo visualizado
        $usuario = \App\Models\Usuario::findOrFail($id);
        
        // 2. Pega os seguidores dele, trazendo junto a foto do perfil associada
        $seguidores = $usuario->seguidores()
                              ->with('perfil:id_perfil,fk_id_usuario,foto') 
                              ->get(['usuarios.id_usuario', 'usuarios.nome_usuario', 'usuarios.nome']);

        // 3. Devolve para o JavaScript em formato JSON limpo
        return response()->json($seguidores);
    }

    // ==========================================
    // BUSCAR LISTA DE QUEM O USUÁRIO SEGUE (JSON)
    // ==========================================
    public function listarSeguindo($id)
    {
        // 1. Busca o usuário dono do perfil que está sendo visualizado
        $usuario = \App\Models\Usuario::findOrFail($id);
        
        // 2. Pega quem esse usuário segue, trazendo junto a foto do perfil associada
        $seguindo = $usuario->seguindo()
                            ->with('perfil:id_perfil,fk_id_usuario,foto')
                            ->get(['usuarios.id_usuario', 'usuarios.nome_usuario', 'usuarios.nome']);

        // 3. Devolve para o JavaScript em formato JSON limpo
        return response()->json($seguindo);
    }

    public function seguir($id_usuario_seguido)
    {
        $id_seguidor = auth('web')->id(); // Pego direto do cookie seguro via JWT

        if (!$id_seguidor) {
            return response()->json(['erro' => 'Não autorizado'], 401);
        }

        // Impede que o usuário siga a si mesmo por segurança
        if ($id_seguidor == $id_usuario_seguido) {
            return response()->json(['erro' => 'Você não pode seguir a si mesmo'], 400);
        }

        // Procura se já existe essa relação no banco
        $seguindoExistente = \App\Models\Seguidor::where('fk_id_seguidor', $id_seguidor)
                                                ->where('fk_id_seguido', $id_usuario_seguido)
                                                ->first();

        if ($seguindoExistente) {
            $seguindoExistente->delete();
            $seguindo = false;
        } else {
            \App\Models\Seguidor::create([
                'fk_id_seguidor' => $id_seguidor,
                'fk_id_seguido' => $id_usuario_seguido
            ]);
            $seguindo = true;
        }

        // Retorna a resposta limpa para o JavaScript atualizar a tela
        return response()->json([
            'status' => 'sucesso',
            'seguindo' => $seguindo
        ]);
    }
}