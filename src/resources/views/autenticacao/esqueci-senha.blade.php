@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endpush

@section('titulo', 'Recuperar Senha')

@section('conteudo')
<div class="painel-autenticacao centralizado-texto">
    <h2 class="titulo-boas-vindas">Recuperar Senha</h2>
    <p class="subtitulo-auth">Informe seu e-mail para receber as instruções.</p>

    @if(session('sucesso'))
        <div class="alerta-sucesso">
            {{ session('sucesso') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mensagem-erro">
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('senha.enviar') }}" method="POST" class="formulario-auth alinhado-esquerda">
        @csrf

        <div class="grupo-campo">
            <label class="rotulo-campo">E-mail</label>
            <input type="email" name="email" required class="campo-texto">
        </div>

        <div class="espacamento-botao">
            <button type="submit" class="botao-roxo total-width">
                Enviar
            </button>
        </div>
    </form>

    <div class="bloco-rodape-auth">
        <a href="{{ route('login') }}" class="botao-substituto total-width">
            Voltar para login
        </a>
    </div>
</div>
@endsection