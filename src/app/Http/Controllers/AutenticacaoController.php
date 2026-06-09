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
        $campoTipo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nome_usuario';

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

        'nome_usuario' => [
            'required',
            'string',
            'max:30',
            'unique:usuarios,nome_usuario',
            'regex:/^[a-zA-Z0-9._-]+$/',
        ],

        'email' => 'required|string|email|max:255|unique:usuarios,email',
        'senha' => 'required|string|min:6|confirmed',
    ], [
        'nome.required' => 'O nome é obrigatório.',
        'nome.max' => 'O nome deve ter no máximo 255 caracteres.',

        'nome_usuario.required' => 'O nome de usuário é obrigatório.',
        'nome_usuario.max' => 'O nome de usuário deve ter no máximo 30 caracteres.',
        'nome_usuario.unique' => 'Esse nome de usuário já está em uso.',
        'nome_usuario.regex' => 'O nome de usuário só pode conter letras, números, ponto, traço e underline.',

        'email.required' => 'O e-mail é obrigatório.',
        'email.email' => 'Digite um e-mail válido.',
        'email.unique' => 'Esse e-mail já está cadastrado.',

        'senha.required' => 'A senha é obrigatória.',
        'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
        'senha.confirmed' => 'A confirmação de senha não confere.',
    ]);

        $usuario = Usuario::create([
            'nome' => $requisicao->nome,
            'nome_usuario' => strtolower($requisicao->nome_usuario), // Salva sempre em minúsculo para padronizar
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
        // CORREÇÃO: Mudamos de 'sair()' para 'logout()'
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Se a sua rota da tela inicial se chamar 'login', mude aqui para 'login'
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