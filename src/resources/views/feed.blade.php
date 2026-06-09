@extends('layouts.app')

@section('titulo', 'Feed Principal')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/feed.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('conteudo')
<div class="container-feed">

    <main class="conteudo-principal">

        @forelse($publicacoes as $post)
            <article class="cartao-post">

               @if($post->categorias)
                    <div class="categorias-post">
                        @foreach(explode(',', $post->categorias) as $categoria)
                            <span class="categoria-badge">
                                {{ trim($categoria) }}
                            </span>
                        @endforeach
                    </div>
                @endif
                <div class="cabecalho-post" style="position: relative; z-index: 2;">
                    <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" style="text-decoration: none;">
                        <img src="{{ $post->usuario->perfil && $post->usuario->perfil->foto ? asset('storage/' . $post->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                             class="foto-autor" 
                             alt="Avatar"
                             style="cursor: pointer; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </a>
                    
                    <div class="info-autor">
                        <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" style="text-decoration: none; color: inherit;">
                            <h4>{{ $post->usuario->nome_usuario }}</h4>
                        </a>
                        <span>{{ $post->data_publicacao->diffForHumans() }}</span>                    </div>
                </div>
                
                <div class="corpo-post">
                    <h3>
                        <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="link-card-estendido">
                            {{ $post->titulo }}
                        </a>
                    </h3>
                    <p>{{ $post->resumo }}</p>
                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="link-ver-mais">
                        Ver mais
                    </a>
                    
                    @if($post->capa)
                        <img src="{{ asset('storage/' . $post->capa) }}" class="imagem-capa-post" alt="Capa">
                    @endif
                </div>

                <div class="acoes-post" style="position: relative; z-index: 2; display: flex; gap: 15px;">
                    
                    @php
                        // Verifica se o usuário logado já curtiu este post para colorir o coração
                        $ja_curtiu = $post->curtidas->where('fk_id_usuario', Auth::id())->first();
                    @endphp

                    <button type="button" 
                            class="botao-acao btn-curtir {{ $ja_curtiu ? 'curtido' : '' }}" 
                            data-id="{{ $post->id_publicacao }}"
                            data-token="{{ csrf_token() }}"
                            onclick="alternarCurtida(this)"
                            style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                        <i class="bi {{ $ja_curtiu ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        <span class="contador-curtidas">{{ $post->curtidas->count() }}</span>
                    </button>

                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="botao-acao" style="text-decoration: none; display: flex; align-items: center; gap: 5px; color: #65676b;">
                        <i class="bi bi-chat"></i>
                        <span>{{ $post->comentarios->count() }}</span>
                    </a>
                </div>

            </article>
        @empty
            <p>Nenhuma publicação encontrada no momento.</p>
        @endforelse
    </main>
</div>

<a href="{{ route('publicacao.criar') }}" class="botao-flutuante-criar">+</a>
@endsection

@push('scripts')
    <script src="{{ asset('js/detalhes.js') }}"></script>
@endpush