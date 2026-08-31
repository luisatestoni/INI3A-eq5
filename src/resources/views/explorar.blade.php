@extends('layouts.app')

@section('titulo', 'Explorar')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/explorar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('conteudo')
<div class="container-explorar">

    <!-- BARRA DE PESQUISA -->
    <div class="caixa-pesquisa-header">
        <form action="{{ route('explorar') }}" method="GET" class="form-pesquisa">
            <input type="hidden" name="tipo" value="{{ $tipo }}">
            
            <div class="input-wrapper">
                <i class="bi bi-search icone-busca"></i>
                <input 
                    type="text" 
                    name="busca" 
                    value="{{ $busca }}" 
                    placeholder="Pesquisar pessoas, publicações ou temas..." 
                    autocomplete="off"
                    autofocus>
                
                @if(!empty($busca))
                    <a href="{{ route('explorar') }}" class="btn-limpar-busca" title="Limpar busca">
                        <i class="bi bi-x-circle-fill"></i>
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-buscar">Buscar</button>
        </form>
    </div>

    <!-- ABAS DE FILTRO -->
    @if(!empty($busca))
        <div class="abas-explorar">
            <a href="{{ route('explorar', ['busca' => $busca, 'tipo' => 'tudo']) }}" 
               class="aba-item {{ $tipo === 'tudo' ? 'ativa' : '' }}">
               Tudo
            </a>
            <a href="{{ route('explorar', ['busca' => $busca, 'tipo' => 'publicacoes']) }}" 
               class="aba-item {{ $tipo === 'publicacoes' ? 'ativa' : '' }}">
               Publicações ({{ $publicacoes->count() }})
            </a>
            <a href="{{ route('explorar', ['busca' => $busca, 'tipo' => 'usuarios']) }}" 
               class="aba-item {{ $tipo === 'usuarios' ? 'ativa' : '' }}">
               Pessoas ({{ $usuarios->count() }})
            </a>
        </div>
    @endif

    <!-- RESULTADOS -->
    <div class="conteudo-resultados">

        @if(empty($busca))
            <!-- TELA INICIAL ANTES DE PESQUISAR -->
            <div class="estado-inicial-explorar">
                <div class="icone-destaque-explorar">
                    <i class="bi bi-compass"></i>
                </div>
                <h3>Explore no Scribo</h3>
                <p>Procure por autores, títulos de histórias, artigos ou tópicos de seu interesse.</p>
            </div>

        @elseif($usuarios->isEmpty() && $publicacoes->isEmpty())
            <!-- NENHUM RESULTADO -->
            <div class="sem-resultados">
                <i class="bi bi-emoji-frown"></i>
                <h3>Nenhum resultado encontrado</h3>
                <p>Não encontramos nada correspondente a <strong>"{{ $busca }}"</strong>.</p>
            </div>

        @else

            <!-- SEÇÃO DE USUÁRIOS/PESSOAS -->
            @if(($tipo === 'tudo' || $tipo === 'usuarios') && $usuarios->isNotEmpty())
                <section class="secao-resultados">
                    <h3 class="titulo-secao"><i class="bi bi-people"></i> Pessoas</h3>
                    
                    <div class="grid-usuarios-explorar">
                        @foreach($usuarios as $user)
                            <div class="card-usuario-explorar">
                                <a href="{{ route('perfil.exibir', $user->id_usuario) }}" class="avatar-link">
                                    <img src="{{ $user->perfil && $user->perfil->foto ? asset('storage/' . $user->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                                         alt="{{ $user->nome }}" class="avatar-user">
                                </a>

                                <div class="info-user-explorar">
                                    <a href="{{ route('perfil.exibir', $user->id_usuario) }}" class="nome-user">
                                        {{ $user->nome }}
                                    </a>
                                    <span class="username-user">{{ '@' . ($user->nome_usuario ?? 'usuario') }}</span>
                                    
                                    @if($user->perfil && $user->perfil->bio)
                                        <p class="bio-user">{{ Str::limit($user->perfil->bio, 60) }}</p>
                                    @endif
                                </div>

                                <a href="{{ route('perfil.exibir', $user->id_usuario) }}" class="btn-ver-perfil">
                                    Ver perfil
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- SEÇÃO DE PUBLICAÇÕES -->
            @if(($tipo === 'tudo' || $tipo === 'publicacoes') && $publicacoes->isNotEmpty())
                <section class="secao-resultados">
                    <h3 class="titulo-secao"><i class="bi bi-newspaper"></i> Publicações</h3>

                    <div class="lista-posts-explorar">
                        @foreach($publicacoes as $post)
                            <article class="cartao-post-perfil">
                                
                                @if($post->categorias)
                                    <div class="categorias-post-perfil">
                                        @foreach(explode(',', $post->categorias) as $categoria)
                                            <span class="categoria-badge-perfil">
                                                {{ trim($categoria) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="cabecalho-post-perfil">
                                    <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" class="link-autor-perfil">
                                        <img src="{{ $post->usuario->perfil && $post->usuario->perfil->foto ? asset('storage/' . $post->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                                             class="foto-autor-perfil" alt="Avatar">
                                    </a>

                                    <div class="info-autor-perfil">
                                        <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" class="link-nome-autor-perfil">
                                            <h4>{{ $post->usuario->nome_usuario }}</h4>
                                        </a>
                                        <span>{{ \Carbon\Carbon::parse($post->data_publicacao)->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="corpo-post-perfil">
                                    <div class="texto-post-perfil">
                                        <h3>
                                            <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}">
                                                {{ $post->titulo }}
                                            </a>
                                        </h3>
                                        <p>{{ $post->resumo }}</p>
                                        <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="link-ver-mais-perfil">
                                            Ver mais
                                        </a>
                                    </div>

                                    @if($post->capa)
                                        <img src="{{ asset('storage/' . $post->capa) }}" class="imagem-capa-post-perfil">
                                    @endif
                                </div>

                                <div class="acoes-post-perfil">
                                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="botao-acao-perfil">
                                        <i class="bi bi-heart"></i>
                                        <span>{{ $post->curtidas->count() }}</span>
                                    </a>
                                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="botao-acao-perfil">
                                        <i class="bi bi-chat"></i>
                                        <span>{{ $post->comentarios->count() }}</span>
                                    </a>
                                </div>

                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

        @endif

    </div>

</div>
@endsection