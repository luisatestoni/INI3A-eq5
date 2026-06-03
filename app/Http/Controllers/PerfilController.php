<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function exibirPerfil($usuario) {
        $dadosUsuario = Usuario::with(['perfil', 'posts'])->where('usuario', $usuario)->firstOrFail();
        return view('profile.show', compact('dadosUsuario'));
    }

    public function alterarSenha(Request $request) {
        $request->validate([
            'senha_atual' => 'required',
            'nova_senha' => 'required|min:6',
        ]);

        $usuarioLogado = auth()->user();

        if (!Hash::check($request->senha_atual, $usuarioLogado->senha)) {
            return back()->withErrors(['senha_atual' => 'Sua senha atual não confere.']);
        }

        // Correção de tipagem para o método update rodar sem problemas com a IDE
        Usuario::where('id_usuario', $usuarioLogado->id_usuario)->update([
            'senha' => Hash::make($request->nova_senha)
        ]);

        return redirect()->route('perfil.exibir', $usuarioLogado->usuario)->with('sucesso', 'Senha alterada!');
    }
}
