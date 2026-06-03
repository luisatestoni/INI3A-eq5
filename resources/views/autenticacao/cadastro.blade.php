@extends('layouts.visitante')

@section('conteudo')
<div class="painel-autenticacao">
    <h2 class="titulo-painel">Comece a usar o Scribo</h2>

    <form action="{{ route('cadastro') }}" method="POST" class="formulario-auth">
        @csrf

        <div class="grupo-campo">
            <label class="rotulo-campo">E-mail</label>
            <input type="email" name="email" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Senha</label>
            <input type="password" name="senha" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Data de nascimento</label>
            <div class="grade-data-nascimento">
                <input type="number" name="dia_nascimento" placeholder="Dia" min="1" max="31" required class="campo-texto centralizado">
                <input type="number" name="mes_nascimento" placeholder="Mês" min="1" max="12" required class="campo-texto centralizado">
                <input type="number" name="ano_nascimento" placeholder="Ano" min="1900" max="2026" required class="campo-texto centralizado">
            </div>
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Nome</label>
            <input type="text" name="nome" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Nome do Usuário</label>
            <input type="text" name="usuario" required class="campo-texto">
        </div>

        <div class="acoes-formulario">
            <button type="submit" class="botao-roxo total-width">Enviar</button>
            <a href="{{ route('login') }}" class="botao-substituto">Já tenho conta</a>
        </div>
    </form>
</div>
@endsection