@extends('layouts.app')

@section('conteudo')
<div class="painel-autenticacao centralizado-texto">
    <h2 class="titulo-boas-vindas">Seja Bem-Vindo</h2>
    <p class="subtitulo-auth">Faça seu login para acessar o Scribo</p>

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
    </form>

    <div class="bloco-rodape-auth">
        <p class="texto-esqueceu">Esqueceu a senha?</p>
        <a href="{{ route('senha.esqueci') }}" class="link-clique-aqui">Clique aqui</a>
    </div>
</div>
@endsection