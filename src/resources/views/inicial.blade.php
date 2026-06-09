@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inicial.css') }}">


@endpush

@section('conteudo')
<div class="boas-vindas-container">
    <div class="coluna-texto">
        <h1>Escreva.<br>Compartilhe.<br>Conecte<span>.</span></h1>
        <p>
            A <strong>Scribo</strong> é a rede social para quem transforma
            ideias em palavras.
        </p>
        <div class="acoes-iniciais">
            <a href="{{ route('cadastro') }}" class="botao-laranja">Criar conta</a>
            <a href="{{ route('login') }}" class="botao-link">Já tenho conta</a>
        </div>
    </div>
    <div class="coluna-ilustracao">
        <!-- Elemento flutuante simulando o card com a caneta da foto -->
        <div class="card-ilustrado">
            All's fair in art
            and writing.
        </div>  
    </div>
</div>
@endsection