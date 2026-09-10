<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuario;
use App\Models\Perfil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Redireciona para a página de login do Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Recebe o retorno do Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Busca o usuário APENAS pelo e-mail
            $usuario = Usuario::where('email', $googleUser->getEmail())->first();

            // Se o usuário não existir no banco, cria um novo
            if (!$usuario) {
                // Gera um nome_usuario único baseado na parte antes do @ do e-mail
                $usernameBase = Str::slug(explode('@', $googleUser->getEmail())[0], '');
                $username = $usernameBase;
                $contador = 1;

                while (Usuario::where('nome_usuario', $username)->exists()) {
                    $username = $usernameBase . $contador++;
                }

                // Cria o Usuário na tabela 'usuarios'
                $usuario = Usuario::create([
                    'nome' => $googleUser->getName() ?? 'Usuário',
                    'nome_usuario' => strtolower($username),
                    'email' => $googleUser->getEmail(),
                    'senha' => Hash::make(Str::random(16)), // Senha aleatória segura
                    'status' => 'ativo',
                ]);

                // Cria o Perfil vinculado
                Perfil::create([
                    'fk_id_usuario' => $usuario->id_usuario,
                    'bio' => 'Olá! Cheguei pelo Google.',
                    'tipo' => 'comum'
                ]);
            }

            // Realiza o login do usuário
            Auth::login($usuario);

            return redirect()->intended('/feed');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['login' => 'Erro ao autenticar com o Google.']);
        }
    }
}