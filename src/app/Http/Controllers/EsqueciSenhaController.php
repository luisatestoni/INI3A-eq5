<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EsqueciSenhaController extends Controller
{
    public function enviarRecuperacao(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email'
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos nenhum usuário com este e-mail.'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $link = route('senha.redefinir', ['token' => $token, 'email' => $request->email]);

        Mail::send([], [], function ($message) use ($usuario, $link) {
            $message->to($usuario->email)
                ->subject('Recuperação de Senha - Scribo')
                ->html("
                    <div style='font-family: sans-serif; max-width: 500px; margin: 0 auto; color: #333;'>
                        <h2>Olá, {$usuario->nome}!</h2>
                        <p>Você solicitou a recuperação de senha da sua conta no Scribo.</p>
                        <p>Clique no botão abaixo para redefinir sua senha:</p>
                        <p style='margin: 25px 0;'>
                            <a href='{$link}' style='padding: 12px 24px; background-color: #6b46c1; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Redefinir Minha Senha</a>
                        </p>
                        <p><small>Se você não fez esta solicitação, desconsidere este e-mail.</small></p>
                    </div>
                ");
        });

        return back()->with('sucesso', 'Enviamos o link de recuperação para o seu e-mail!');
    }

    public function exibirRedefinirSenha(Request $request, $token)
    {
        return view('autenticacao.redefinir-senha', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function redefinirSenha(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'senha' => 'required|string|min:6|confirmed',
        ], [
            'senha.required' => 'A nova senha é obrigatória.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'senha.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $registro = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$registro) {
            return back()->withErrors(['email' => 'Token de redefinição inválido ou expirado.']);
        }

        $usuario = Usuario::where('email', $request->email)->first();
        $usuario->update([
            'senha' => Hash::make($request->senha)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('sucesso', 'Sua senha foi alterada com sucesso! Faça login.');
    }
}