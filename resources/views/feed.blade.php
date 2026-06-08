@extends('layouts.app')

@push('estilos')
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/feed.css') }}">
@endpush

@section('conteudo_interno')
<div class="bloco-feed">
    
    <div class="abas-navegacao">
        <a href="?aba=para-voce" class="aba-item {{ $abaAtiva == 'para-voce' ? 'ativa' : '' }}">Para você</a>
        <a href="?aba=seguindo" class="aba-item {{ $abaAtiva == 'seguindo' ? 'ativa' : '' }}">Seguindo</a>
        <a href="?aba=tendencias" class="aba-item {{ $abaAtiva == 'tendencias' ? 'ativa' : '' }}">Tendências</a>
        <a href="?aba=recentes" class="aba-item {{ $abaAtiva == 'recentes' ? 'ativa' : '' }}">Recentes</a>
    </div>

    <div class="lista-postagens-feed">
        @foreach($listaPosts ?? [] as $post)
        <div class="card-postagem">
            
            <div class="conteudo-card-esquerdo">
                <div class="dados-autor-card">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80" class="foto-autor-miniatura">

                    <span class="nome-autor-negrito">{{ $post->usuario->usuario }}</span>
                    <span class="tempo-postagem">• {{ $post->created_at->diffForHumans() }}</span>
                </div>

                <h3 class="titulo-postagem-card">{{ $post->titulo }}</h3>
                <p class="resumo-postagem-card">{{ $post->resumo }}</p>
                
                <a href="{{ route('post.exibir', $post->id_post) }}" class="link-ver-mais">
                    Ver mais <span>→</span>
                </a>

                <div class="reacoes-postagem-barra">
                    <button class="botao-reacao-social">❤️ <span class="contador-reacao">124</span></button>
                    <button class="botao-reacao-social">💬 <span class="contador-reacao">124</span></button>
                    <button class="botao-reacao-social-marcar">🔖</button>
                    <button class="botao-reacao-social-marcar">📤</button>
                </div>
            </div>

            <div class="capa-card-direita">
                @if($post->capa)
                    <img src="{{ asset('storage/' . $post->capa) }}" class="imagem-capa-feed">
                @else
                    <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=250&q=80" class="imagem-capa-feed">
                @endif
                <span class="tag-categoria-post">Texto</span>
            </div>

            <div class="bloco-menu-admin-post">
                <button onclick="abrirModalExclusao()" class="botao-tres-pontos">•••</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="modal-excluir" class="tela-modal escondido">
    <div class="caixa-modal-conteudo">
        <p class="texto-aviso-modal">Deseja excluir essa publicação?</p>
        <div class="botoes-modal-grupo">
            <button onclick="fecharModalExclusao()" class="botao-modal-cancelar">Cancelar</button>
            <button class="botao-modal-confirmar">Excluir</button>
        </div>
    </div>
</div>

<script>
    function abrirModalExclusao() {
        document.getElementById('modal-excluir').style.display = 'flex';
    }
    document.getElementById('modal-excluir').style.display = 'none';
    function fecharModalExclusao() {
        document.getElementById('modal-excluir').style.display = 'none';
    }
</script>
@endsection