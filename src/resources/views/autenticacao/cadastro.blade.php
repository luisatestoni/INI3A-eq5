@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endpush

    @section('conteudo')
    <div class="painel-autenticacao">
        <h2 class="titulo-painel">Comece a usar o Scribo</h2>
        @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('cadastro') }}" method="POST" class="formulario-auth">
        @csrf

        <div class="grupo-campo">
            <label class="rotulo-campo">E-mail</label>
            <input type="email" name="email" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Senha</label>
            <input type="password" name="senha" required class="campo-texto"  placeholder="Mínimo 8 caracteres, incluindo letras e números">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Confirmar senha</label>
            <input type="password" name="senha_confirmation" required class="campo-texto" placeholder="Confirme sua senha">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Nome</label>
            <input type="text" name="nome" required class="campo-texto">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Nome do Usuário</label>
            <input type="text" name="nome_usuario" required class="campo-texto">
        </div>


        <div class="acoes-formulario">
            <button type="submit" class="botao-roxo total-width">Enviar</button>
            <a href="{{ route('login') }}" class="botao-substituto">Já tenho conta</a>
        </div>
    </form>
</div>
@endsection