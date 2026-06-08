@extends('layouts.app')

@section('titulo', $post->titulo)

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/detalhes-publicacao.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('conteudo')

<div class="container-detalhes">

    <article class="card-detalhes">

        <div class="cabecalho-post">

            <div class="autor-post">

                <img
                    src="{{ $post->usuario->perfil && $post->usuario->perfil->foto
                        ? asset('storage/'.$post->usuario->perfil->foto)
                        : asset('imagens/padrao-avatar.png') }}"
                    class="avatar-autor">

                <div>
                    <h4>{{ $post->usuario->nome }}</h4>

                    <span>
                        {{ \Carbon\Carbon::parse($post->data_publicacao)->diffForHumans() }}
                    </span>
                </div>

            </div>

            @if($post->categorias)
                <div class="tag-categoria">
                    {{ $post->categorias }}
                </div>
            @endif

        </div>

        @if($post->capa)
            <img
                src="{{ asset('storage/'.$post->capa) }}"
                class="imagem-post">
        @endif

        <div class="conteudo-post">

            <h1>{{ $post->titulo }}</h1>

            @if($post->resumo)
                <p class="resumo-post">
                    {{ $post->resumo }}
                </p>
            @endif

            <div class="texto-post">
                {!! nl2br(e($post->conteudo)) !!}
            </div>

        </div>

        <div class="barra-acoes">

            <form
                action="{{ route('publicacao.curtir', $post->id_publicacao) }}"
                method="POST">

                @csrf

                <button class="botao-acao">

                    <i class="bi bi-heart"></i>

                    <span>
                        {{ $post->curtidas->count() }}
                    </span>

                </button>

            </form>

            <div class="botao-acao">

                <i class="bi bi-chat"></i>

                <span>
                    {{ $post->comentarios->count() }}
                </span>

            </div>

            <button class="botao-acao">

                <i class="bi bi-bookmark"></i>

            </button>

            <button class="botao-acao">

                <i class="bi bi-share"></i>

            </button>

        </div>

        <div class="secao-comentarios">

            <h3>
                Comentários ({{ $post->comentarios->count() }})
            </h3>

            <form
                action="{{ route('comentario.salvar') }}"
                method="POST"
                class="form-comentario">

                @csrf

                <input
                    type="hidden"
                    name="id_publicacao"
                    value="{{ $post->id_publicacao }}">

                <input
                    type="text"
                    name="conteudo"
                    placeholder="Escreva um comentário..."
                    required>

                <button type="submit">
                    Publicar
                </button>

            </form>

            @forelse($post->comentarios as $comentario)

                <div class="comentario">

                    <img
                        src="{{ $comentario->usuario->perfil && $comentario->usuario->perfil->foto
                            ? asset('storage/'.$comentario->usuario->perfil->foto)
                            : asset('imagens/padrao-avatar.png') }}"
                        class="avatar-comentario">

                    <div class="corpo-comentario">

                        <div class="topo-comentario">

                            <strong>
                                {{ $comentario->usuario->nome }}
                            </strong>

                        </div>

                        <p>
                            {{ $comentario->conteudo }}
                        </p>

                    </div>

                </div>

            @empty

                <div class="sem-comentarios">
                    Seja o primeiro a comentar.
                </div>

            @endforelse

        </div>

    </article>

</div>

@endsection