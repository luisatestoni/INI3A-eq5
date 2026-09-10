@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endpush

@section('titulo', 'Login')

@section('conteudo')
<div class="painel-autenticacao centralizado-texto">
    <h2 class="titulo-boas-vindas">Seja Bem-Vindo</h2>
    <p class="subtitulo-auth">Faça seu login para acessar o Scribo</p>

    @if ($errors->any())
        <div class="mensagem-erro">
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="formulario-auth alinhado-esquerda">
        @csrf

        <div class="grupo-campo">
            <label class="rotulo-campo">E-mail ou Usuário</label>
            <input type="text" name="login" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Senha</label>
            <input type="password" name="senha" required class="campo-texto">
        </div>

        <div class="espacamento-botao">
            <button type="submit" class="botao-roxo total-width">Acessar</button>
        </div>
        <!-- Divisória -->
        <div class="divisor-auth">
            <hr class="linha-divisor">
            <span class="texto-divisor">OU</span>
            <hr class="linha-divisor">
        </div>

        <!-- Botão do Google -->
        <div class="container-google">
            <a href="{{ route('login.google') }}" class="botao-substituto total-width botao-google">
                <svg width="18" height="18" viewBox="0 0 24 24" style="margin-right: 8px;">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Entrar com o Google
            </a>
        </div>
    </form>

    <div class="bloco-rodape-auth">
        <p class="texto-esqueceu">Esqueceu a senha?</p>
        <a href="{{ route('senha.esqueci') }}" class="link-clique-aqui">Clique aqui</a>
    </div>

    <div class="bloco-cadastro-auth">
        <p class="texto-cadastro">Ainda não possui uma conta?</p>
        <a href="{{ route('cadastro') }}" class="link-clique-aqui">Cadastre-se</a>
    </div>
</div>
@endsection