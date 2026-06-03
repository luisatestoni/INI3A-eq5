<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutenticacaoController extends Controller
{
    public function exibirLogin() {
        return view('auth.login');
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

    public function exibirCadastro() {
        return view('auth.register');
    }

    public function cadastrar(Request $request) {
        $request->validate([
            'nome' => 'required|string|max:100',
            'usuario' => 'required|string|unique:usuarios,usuario|max:50',
            'email' => 'required|string|email|unique:usuarios,email|max:150',
            'senha' => 'required|string|min:6',
            'dia_nascimento' => 'required|numeric|between:1,31',
            'mes_nascimento' => 'required|numeric|between:1,12',
            'ano_nascimento' => 'required|numeric|between:1920,' . date('Y'),
        ]);

        $dataNascimentoCompleta = "{$request->ano_nascimento}-{$request->mes_nascimento}-{$request->dia_nascimento}";

        $novoUsuario = Usuario::create([
            'nome' => $request->nome,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'data_nascimento' => $dataNascimentoCompleta,
        ]);

        Perfil::create([
            'id_usuario' => $novoUsuario->id_usuario,
            'tipo' => 'leitor',
            'bio' => 'Olá! Entrei agora no Scribo.',
        ]);

        Auth::login($novoUsuario);

        return redirect()->route('feed');
    }

    public function sair(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('inicial');
    }
}