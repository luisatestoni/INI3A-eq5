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
use App\Notifications\GeralNotification;

class PerfilController extends BaseController
{
    public function exibir(string $nome_usuario)
    {
        $usuario = Usuario::with([
            'perfil',
            'publicacoes' => function ($query) {
                $query->with(['usuario.perfil', 'curtidas', 'comentarios'])
                    ->orderBy('data_publicacao', 'desc');
            },
            'seguidores',
            'seguindo'
        ])->where('nome_usuario', $nome_usuario)->firstOrFail();

        return view('perfil.exibir', compact('usuario'));
    }

    public function editar(string $nome_usuario)
    {
        $usuario = Usuario::with('perfil')->where('nome_usuario', $nome_usuario)->firstOrFail();

        // SEGURANÇA MÁXIMA: Verifica se quem está logado é o dono do perfil
        if (Auth::id() != $usuario->id_usuario) {
            return redirect()->route('feed')->withErrors([
                'permissao' => 'Você não tem permissão para editar o perfil de outra pessoa!'
            ]);
        }

        return view('perfil.editar', compact('usuario'));
    }

    public function atualizar(Request $request, int $id_usuario)
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
            ->route('perfil.exibir', $usuario->nome_usuario)
            ->with('sucesso', 'Perfil atualizado com sucesso!');
    }

    // Remove permanentemente o usuário e limpa a sessão/cookie
    public function excluirConta()
    {
        $idUsuarioLogado = Auth::id();

        if (!$idUsuarioLogado) {
            return redirect()->route('login');
        }

        $usuario = Usuario::findOrFail($idUsuarioLogado);
        $usuario->delete();

        $cookieLimpo = cookie()->forget('jwt_token');

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

    public function listarSeguidores(string $nome_usuario)
    {
        $usuario = Usuario::where('nome_usuario', $nome_usuario)->firstOrFail();
        
        $seguidores = $usuario->seguidores()
                              ->with('perfil:id_perfil,fk_id_usuario,foto') 
                              ->get(['usuarios.id_usuario', 'usuarios.nome_usuario', 'usuarios.nome']);

        return response()->json($seguidores);
    }

    public function listarSeguindo(string $nome_usuario)
    {
        $usuario = Usuario::where('nome_usuario', $nome_usuario)->firstOrFail();
        
        $seguindo = $usuario->seguindo()
                            ->with('perfil:id_perfil,fk_id_usuario,foto')
                            ->get(['usuarios.id_usuario', 'usuarios.nome_usuario', 'usuarios.nome']);

        return response()->json($seguindo);
    }

    public function seguir(string $nome_usuario)
    {
        $id_seguidor = Auth::id();

        if (!$id_seguidor) {
            return response()->json(['erro' => 'Não autorizado'], 401);
        }

        $usuarioAlvo = Usuario::where('nome_usuario', $nome_usuario)->firstOrFail();

        // Impede que o usuário siga a si mesmo por segurança
        if ($id_seguidor == $usuarioAlvo->id_usuario) {
            return response()->json(['erro' => 'Você não pode seguir a si mesmo'], 400);
        }

        // Procura se já existe essa relação no banco
        $seguindoExistente = Seguidor::where('fk_id_seguidor', $id_seguidor)
                                     ->where('fk_id_seguido', $usuarioAlvo->id_usuario)
                                     ->first();

        if ($seguindoExistente) {
            $seguindoExistente->delete();
            $seguindo = false;
        } else {
            Seguidor::create([
                'fk_id_seguidor' => $id_seguidor,
                'fk_id_seguido' => $usuarioAlvo->id_usuario
            ]);
            $seguindo = true;

            // Dispara notificação para o usuário seguido
            $usuarioLogado = Auth::user();
            $usuarioAlvo->notify(new GeralNotification(
                $usuarioLogado,
                'seguidor',
                'começou a te seguir no Scribo.',
                route('perfil.exibir', $usuarioLogado->nome_usuario)
            ));
        }

        $totalSeguidores = $usuarioAlvo->seguidores()->count();

        return response()->json([
            'status' => 'sucesso',
            'seguindo' => $seguindo,
            'total_seguidores' => $totalSeguidores
        ]);
    }
}