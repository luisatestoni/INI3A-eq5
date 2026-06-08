<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario; // ESSA LINHA SEPARA O ERRO DO SUCESSO!
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Perfil; // PARA CRIAR O PERFIL JUNTO COM O USUÁRIO

class AutenticacaoController extends Controller
{
    public function exibirLogin() {
        return view('autenticacao.login');
    }

    public function exibirCadastro() {
        return view('autenticacao.cadastro');
    }

    public function logar(Request $request) {
        $dadosValidados = $request->validate([
            'login' => 'required',
            'senha' => 'required',
        ]);

        // Verifica se digitou e-mail ou nome de usuário
        $campoTipo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'usuario';

        // Tentativa de autenticação manual por conta da coluna personalizada 'senha'
        $usuarioObtido = Usuario::where($campoTipo, $request->login)->first();

        if ($usuarioObtido && Hash::check($request->senha, $usuarioObtido->senha)) {
            Auth::login($usuarioObtido);
            $request->session()->regenerate();
            return redirect()->intended('/feed');
        }

        return back()->withErrors([
            'login' => 'As credenciais fornecidas não conferem.',
        ]);
    }

    public function registrar(Request $requisicao)
    {
        $requisicao->validate([
            'nome' => 'required|string|max:255',
            'username' => 'required|string|alpha_num|max:30|unique:usuarios,username', // VALIDAÇÃO: apenas letras e números, único no banco
            'email' => 'required|string|email|max:255|unique:usuarios',
            'senha' => 'required|string|min:6|confirmed',
        ]);

        $usuario = Usuario::create([
            'nome' => $requisicao->nome,
            'username' => strtolower($requisicao->username), // Salva sempre em minúsculo para padronizar
            'email' => $requisicao->email,
            'senha' => Hash::make($requisicao->senha),
            'status' => 'ativo',
        ]);

        // Cria o perfil...
        Perfil::create([
            'fk_id_usuario' => $usuario->id_usuario,
            'bio' => 'Olá! Acabei de me juntar ao Scribo.',
            'tipo' => 'comum'
        ]);

        Auth::login($usuario);
        return redirect()->route('feed');
    }

    public function sair(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('inicial');
    }

    public function enviarRecuperacao(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        return back()->with(
            'sucesso',
            'Caso o e-mail exista, enviaremos as instruções de recuperação.'
        );
    }
}