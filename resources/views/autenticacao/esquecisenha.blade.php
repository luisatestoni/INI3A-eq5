@extends('layouts.visitante')

@section('conteudo')
<div class="painel-autenticacao centralizado-texto">
    <h2 class="titulo-recuperacao">Encontre sua conta Scribo</h2>
    <p class="subtitulo-auth">Insira seu E-mail cadastrado</p>

    <form action="#" method="POST" class="formulario-auth alinhado-esquerda">
        @csrf

        <div class="grupo-campo">
            <label class="rotulo-campo">E-mail</label>
            <input type="email" name="email" required class="campo-texto">
        </div>

        <button type="submit" class="botao-roxo total-width">Enviar</button>
    </form>

    <div class="bloco-voltar-login">
        <a href="{{ route('login') }}" class="link-voltar-uppercase">Voltar para login</a>
    </div>
</div>
@endsection