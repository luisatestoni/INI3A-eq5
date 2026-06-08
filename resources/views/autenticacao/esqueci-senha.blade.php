@extends('layouts.app')

@section('titulo', 'Recuperar Senha')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
@endpush

@section('conteudo')
<div class="caixa-autenticacao login-box">

    <h2>Recuperar Senha</h2>

    <p class="subtitulo-login">
        Informe seu e-mail para receber as instruções.
    </p>

    @if(session('sucesso'))
        <div class="alerta-sucesso">
            {{ session('sucesso') }}
        </div>
    @endif

    <form action="{{ route('senha.enviar') }}" method="POST">
        @csrf

        <div class="campo-grupo">
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>

        <button type="submit" class="botao-roxo">
            Enviar
        </button>
    </form>

    <div class="links-login">
        <a href="{{ route('login') }}">
            Voltar para login
        </a>

</div>
@endsection