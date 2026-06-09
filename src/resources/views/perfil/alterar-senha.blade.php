@extends('layouts.app')

@section('titulo', 'Alterar Senha - Scribo')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
@endpush

@section('conteudo')
<div class="painel-autenticacao">
    <h2 class="titulo-painel">Alterar sua senha</h2>
    
    @if ($errors->any())
        <div class="alert-erro">
            <ul>
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('sucesso'))
        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
            {{ session('sucesso') }}
        </div>
    @endif

    <form action="{{ route('senha.atualizar') }}" method="POST" class="formulario-auth">
        @csrf
        @method('PUT') 
        
        <div class="grupo-campo">
            <label class="rotulo-campo">Senha Atual</label>
            <input type="password" name="senha_atual" required class="campo-texto" placeholder="Digite sua senha atual">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Nova Senha</label>
            <input type="password" name="nova_senha" required class="campo-texto" placeholder="Crie uma nova senha forte">
        </div>

        <div class="grupo-campo">
            <label class="rotulo-campo">Confirmar Nova Senha</label>
            <input type="password" name="nova_senha_confirmation" required class="campo-texto" placeholder="Digite a nova senha novamente">
        </div>

        <div class="acoes-formulario">
            <button type="submit" class="botao-roxo total-width">Atualizar Senha</button>
            <a href="{{ route('perfil.exibir', Auth::id()) }}" class="botao-substituto">Voltar ao perfil</a>
        </div>
    </form>
</div>
@endsection