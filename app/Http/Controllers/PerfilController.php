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
                $query->orderBy('data_publicacao', 'desc');
            },
            'seguidores',
            'seguindo'
        ])->findOrFail($id);

        return view('perfil.exibir', compact('usuario'));
    }

    public function editar()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Usuario::with('perfil')->findOrFail(Auth::id());

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

    public function excluirConta()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Usuario::findOrFail(Auth::id());

        Auth::logout();

        if ($usuario->perfil && $usuario->perfil->foto) {
            Storage::disk('public')->delete($usuario->perfil->foto);
        }

        if ($usuario->perfil && $usuario->perfil->capa) {
            Storage::disk('public')->delete($usuario->perfil->capa);
        }

        $usuario->delete();

        return redirect()
            ->route('inicial')
            ->with('sucesso', 'Conta excluída com sucesso!');
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
        $seguidores = Seguidor::with('seguidor.perfil')
            ->where('fk_id_seguido', $id)
            ->get();

        return response()->json($seguidores);
    }

    public function listarSeguindo($id)
    {
        $seguindo = Seguidor::with('seguido.perfil')
            ->where('fk_id_seguidor', $id)
            ->get();

        return response()->json($seguindo);
    }

    public function seguir($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Usuário não autenticado.'
            ], 401);
        }

        $usuarioLogado = Usuario::findOrFail(Auth::id());

        if ($usuarioLogado->id_usuario == $id) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Você não pode seguir a si mesmo.'
            ], 400);
        }

        $relacao = Seguidor::where('fk_id_seguidor', $usuarioLogado->id_usuario)
            ->where('fk_id_seguido', $id)
            ->first();

        if ($relacao) {
            $relacao->delete();

            return response()->json([
                'status' => 'sucesso',
                'seguindo' => false,
            ]);
        }

        $novaRelacao = new Seguidor();
        $novaRelacao->fk_id_seguidor = $usuarioLogado->id_usuario;
        $novaRelacao->fk_id_seguido = $id;
        $novaRelacao->save();

        return response()->json([
            'status' => 'sucesso',
            'seguindo' => true,
        ]);
    }
}