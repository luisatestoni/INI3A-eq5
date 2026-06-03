@extends('layouts.visitante')

@section('conteudo')
<div class="grade-landing">
    
    <div class="bloco-boas-vindas">
        <h1 class="titulo-landing">
            Escreva.<br>
            Compartilhe.<br>
            <span class="destaque-laranha">Conecte.</span>
        </h1>
        
        <div class="linha-separadora"></div>
        
        <p class="texto-descricao">
            A <span class="marca-texto">Scribo.</span> é a rede social para quem transforma ideias em palavras.
        </p>

        <div class="grupo-botoes-landing">
            <a href="{{ route('cadastro') }}" class="link-acao-principal">
                <span>Criar conta</span>
                <span>→</span>
            </a>
            <a href="{{ route('login') }}" class="link-acao-secundario">
                <span>Entrar</span>
                <span>→</span>
            </a>
        </div>
    </div>

    <div class="bloco-ilustracao">
        <div class="mancha-decorativa"></div>
        
        <div class="card-citacao">
            <span class="aspas-serif">“</span>
            <p class="frase-citacao">All's fair in art and writing.</p>
            <div class="pequena-linha"></div>
            <div class="icone-pena">🪶</div>
        </div>
    </div>

</div>
@endsection