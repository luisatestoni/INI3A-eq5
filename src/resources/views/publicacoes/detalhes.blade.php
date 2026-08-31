@extends('layouts.app')

@section('titulo', $post->titulo)

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/detalhes-publicacao.css') }}">
<link rel="stylesheet" href="{{ asset('css/global.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('conteudo')
<div class="conteudo-principal-site com-barra-lateral">
<div class="container-botao-voltar">
    <a href="{{ route('feed') }}" class="btn-voltar-link" title="Voltar para a página anterior" onclick="if(document.referrer) { event.preventDefault(); history.back(); }">
        <i class="bi bi-arrow-left"></i> 
        Voltar
    </a>
</div>
<div class="container-detalhes">

    <article class="card-detalhes" style="position: relative;">

        <div class="cabecalho-post">

            <div class="autor-post">
                <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                    <img src="{{ $post->usuario->perfil && $post->usuario->perfil->foto
                            ? asset('storage/'.$post->usuario->perfil->foto)
                            : asset('imagens/perfil-v1.png') }}"
                        class="avatar-autor" 
                        style="cursor: pointer; transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'">
                    
                    <div style="color: initial;">
                        <h4>{{ $post->usuario->nome_usuario}}</h4>
                        <span>
                            {{ $post->data_publicacao->diffForHumans() }}
                        </span>
                    </div>
                </a>
            </div>

            <div class="cabecalho-direita" style="display: flex; align-items: center; gap: 15px;">
                @if($post->categorias)
                    <div class="tag-categoria">
                        {{ $post->categorias }}
                    </div>
                @endif

                <!-- SEGURANÇA: Só renderiza o menu se o usuário estiver logado E for o dono do post -->
                @if(Auth::check() && Auth::id() == $post->usuario->id_usuario)
                <div class="container-opcoes-post">
                    <button class="btn-tres-pontinhos" onclick="alternarMenuPost(event, 'menu-{{ $post->id_publicacao }}')">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    
                    <div id="menu-{{ $post->id_publicacao }}" class="menu-opcoes-post">
                        <a href="{{ route('publicacao.editar', $post->id_publicacao) }}" class="item-opcao-post">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        
                        <form action="{{ route('publicacao.deletar', $post->id_publicacao) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="item-opcao-post deletar">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

        </div>

        @if($post->capa)
            <img src="{{ asset('storage/'.$post->capa) }}" class="imagem-post">
        @endif

        <div class="conteudo-post">
            <h1>{{ $post->titulo }}</h1>

            @if($post->resumo)
                <p class="resumo-post">{{ $post->resumo }}</p>
            @endif

            <div class="texto-post">
                {!! nl2br(e($post->conteudo)) !!}
            </div>

            @if($post->podcast)
                <div class="container-podcast" style="margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h5 style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-mic-fill" style="color: #007bff;"></i> Ouvir Episódio do Podcast
                    </h5>
                    <audio controls style="width: 100%;">
                        <source src="{{ asset('storage/' . $post->podcast) }}" type="audio/mpeg">
                    </audio>
                </div>
            @endif
        </div>

        <!-- Barra de Ações (Curtir, Comentar...) -->
        <div class="barra-acoes">
            <div class="container-curtida">
                @php
                    $ja_curtiu = Auth::check() ? $post->curtidas->where('fk_id_usuario', Auth::id())->first() : null;
                @endphp
                <button type="button" class="botao-acao btn-curtir {{ $ja_curtiu ? 'curtido' : '' }}" data-id="{{ $post->id_publicacao }}" data-token="{{ csrf_token() }}" onclick="event.stopPropagation(); alternarCurtida(this)">
                    <i class="bi {{ $ja_curtiu ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    <span class="contador-curtidas">{{ $post->curtidas->count() }}</span>
                </button>
            </div>
            <div class="botao-acao"><i class="bi bi-chat"></i> <span>{{ $post->comentarios->count() }}</span></div>
            <button class="botao-acao"><i class="bi bi-bookmark"></i></button>
            <button class="botao-acao"><i class="bi bi-share"></i></button>
        </div>

        <!-- Seção de Comentários -->
        <!-- Seção de Comentários -->
    <div class="secao-comentarios">
        <h3>Comentários (<span id="contador-comentarios-titulo">{{ $post->comentarios->count() }}</span>)</h3>

        @if(Auth::check())
        <form action="{{ route('comentario.salvar') }}" method="POST" class="form-comentario" onsubmit="enviarComentarioAssincrono(event, this)">
            @csrf
            <input type="hidden" name="id_publicacao" value="{{ $post->id_publicacao }}">
            <input type="text" name="conteudo" placeholder="Escreva um comentário..." required>
            <button type="submit">Publicar</button>
        </form>
        @endif

        <div class="lista-comentarios">
            @forelse($post->comentarios as $comentario)
                <div class="comentario">
                    <a href="{{ route('perfil.exibir', $comentario->usuario->id_usuario) }}">
                        <img src="{{ $comentario->usuario->perfil && $comentario->usuario->perfil->foto ? asset('storage/'.$comentario->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" class="avatar-comentario">
                    </a>
                    <div class="corpo-comentario">
                        <div class="topo-comentario">
                            <a href="{{ route('perfil.exibir', $comentario->usuario->id_usuario) }}" style="text-decoration: none; color: inherit;">
                                <strong>{{ $comentario->usuario->nome_usuario }}</strong>
                            </a>
                        </div>
                        <p>{{ $comentario->conteudo }}</p>
                    </div>
                </div>
            @empty
                <div class="sem-comentarios">Seja o primeiro a comentar.</div>
            @endforelse
        </div>
    </div>

    </article>

</div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/detalhes.js') }}"></script>
@endpush