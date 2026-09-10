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
    <a href="{{ route('feed') }}" 
       class="btn-voltar-link" 
       title="Voltar para a página anterior" 
       onclick="if(document.referrer && !document.referrer.includes('/editar')) { event.preventDefault(); history.back(); }">
        <i class="bi bi-arrow-left"></i> 
        Voltar
    </a>
</div>
<div class="container-detalhes">

    <article class="card-detalhes" style="position: relative;">

        <div class="cabecalho-post">

            <div class="autor-post">
                <a href="{{ route('perfil.exibir', $post->usuario->nome_usuario) }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
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

                {{-- Exibe o menu se for o AUTOR do post OU se for ADMIN --}}
                @if(Auth::check() && (Auth::id() == $post->usuario->id_usuario || Auth::user()->e_admin))
                <div class="container-opcoes-post">
                    <button class="btn-tres-pontinhos" onclick="alternarMenuPost(event, 'menu-{{ $post->id_publicacao }}')">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    
                    <div id="menu-{{ $post->id_publicacao }}" class="menu-opcoes-post">
                        {{-- Apenas o dono do post pode editar --}}
                        @if(Auth::id() == $post->usuario->id_usuario)
                        <a href="{{ route('publicacao.editar', $post->id_publicacao) }}" class="item-opcao-post">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @endif
                        
                        {{-- O dono OU o Admin podem excluir o post --}}
                        <form action="{{ route('publicacao.deletar', $post->id_publicacao) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="item-opcao-post deletar">
                                <i class="bi bi-trash"></i> 
                                {{ Auth::user()->e_admin && Auth::id() != $post->usuario->id_usuario ? 'Excluir (Admin)' : 'Excluir' }}
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
                <div class="container-podcast">
                    <h5 style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-mic-fill" style="color: #007bff;"></i> Ouvir Episódio do Podcast
                    </h5>
                    <audio controls style="width: 100%;">
                        <source src="{{ asset('storage/' . $post->podcast) }}" type="audio/mpeg">
                    </audio>
                </div>
            @endif
        </div>

        <!-- Barra de Ações -->
        <div class="barra-acoes">

            <!-- Curtir -->
            <div class="container-curtida">
                @php
                    $ja_curtiu = Auth::check() ? $post->curtidas->where('fk_id_usuario', Auth::id())->first() : null;
                @endphp
                <button type="button" 
                        class="botao-acao btn-curtir {{ $ja_curtiu ? 'curtido' : '' }}" 
                        data-id="{{ $post->id_publicacao }}" 
                        data-token="{{ csrf_token() }}" 
                        onclick="event.stopPropagation(); alternarCurtida(this)">
                    <i class="bi {{ $ja_curtiu ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    <span class="contador-curtidas">{{ $post->curtidas->count() }}</span>
                </button>
            </div>

            <!-- Comentários -->
            <div class="botao-acao">
                <i class="bi bi-chat"></i> 
                <span>{{ $post->comentarios->count() }}</span>
            </div>

            <!-- Salvar -->
            <div class="container-salvar">
                @php
                    $ja_salvou = Auth::check() ? $post->salvos->where('fk_id_usuario', Auth::id())->first() : null;
                @endphp
                <button type="button" 
                        class="botao-acao btn-salvar {{ $ja_salvou ? 'salvo' : '' }}" 
                        data-id="{{ $post->id_publicacao }}" 
                        data-token="{{ csrf_token() }}" 
                        onclick="event.stopPropagation(); alternarSalvar(this)">
                    <i class="bi {{ $ja_salvou ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                    <span class="contador-salvos">{{ $post->salvos->count() }}</span>
                </button>
            </div>

            <!-- Compartilhar -->
            <button type="button" 
                    class="botao-acao btn-compartilhar" 
                    data-id="{{ $post->id_publicacao }}"
                    data-titulo="{{ $post->titulo }}"
                    data-url="{{ route('publicacao.detalhes', $post->id_publicacao) }}"
                    onclick="abrirModalCompartilharData(this)">
                <i class="bi bi-share"></i>
                <span class="contador-compartilhamentos">{{ $post->compartilhamentos ?? 0 }}</span>
            </button>

        </div>

        <!-- Seção de Comentários -->
        <div class="secao-comentarios">
            <h3>Comentários (<span id="contador-comentarios-titulo">{{ $post->comentarios->count() }}</span>)</h3>

            @if(Auth::check())
            <div class="container-form-comentario">
                <!-- Indicador de Resposta -->
                <div id="indicador-resposta" class="indicador-resposta" style="display: none; align-items: center; justify-content: space-between; background: #f0ecf7; padding: 6px 12px; border-radius: 6px; margin-bottom: 8px; font-size: 13px; color: #452083;">
                    <span>Respondendo a <strong id="nome-autor-resposta"></strong></span>
                    <button type="button" onclick="cancelarResposta()" style="background: none; border: none; color: #d9534f; cursor: pointer; font-weight: bold; font-size: 14px;">&times; Cancelar</button>
                </div>

                <form action="{{ route('comentario.salvar') }}" method="POST" class="form-comentario" onsubmit="enviarComentarioAssincrono(event, this)">
                    @csrf
                    <input type="hidden" name="id_publicacao" value="{{ $post->id_publicacao }}">
                    <!-- Campo oculta id_pai -->
                    <input type="hidden" name="id_pai" id="input_id_pai" value="">
                    
                    <input type="text" name="conteudo" id="campo_conteudo_comentario" placeholder="Escreva um comentário..." required>
                    <button type="submit">Publicar</button>
                </form>
            </div>
            @endif

            <div class="lista-comentarios">
                @forelse($post->comentarios->whereNull('id_pai') as $comentario)
                    @php $idPai = $comentario->id_comentario ?? $comentario->id; @endphp
                    <div class="comentario" id="comentario-{{ $idPai }}">
                        <a href="{{ route('perfil.exibir', $comentario->usuario->nome_usuario) }}">
                            <img src="{{ $comentario->usuario->perfil && $comentario->usuario->perfil->foto ? asset('storage/'.$comentario->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" class="avatar-comentario">
                        </a>
                        <div class="corpo-comentario" style="width: 100%;">
                            <div class="topo-comentario" style="display: flex; justify-content: space-between; align-items: center;">
                                <a href="{{ route('perfil.exibir', $comentario->usuario->nome_usuario) }}" style="text-decoration: none; color: inherit;">
                                    <strong>{{ $comentario->usuario->nome_usuario }}</strong>
                                </a>
                                <small style="color: #888; font-size: 11px;">{{ $comentario->created_at->diffForHumans() }}</small>
                            </div>
                            <p style="margin: 4px 0 8px;">{{ $comentario->conteudo }}</p>

                            <button type="button" 
                                    class="btn-responder" 
                                    data-id-pai="{{ $idPai }}" 
                                    data-usuario="{{ $comentario->usuario->nome_usuario }}"
                                    onclick="prepararResposta(this.dataset.idPai, this.dataset.usuario)">
                                Responder
                            </button>

                            <!-- Container para respostas filhas -->
                            <div class="respostas-comentario" id="respostas-do-comentario-{{ $idPai }}" style="margin-top: 10px; padding-left: 14px; border-left: 2px solid #ebe5f2;">
                                @if($comentario->respostas && $comentario->respostas->count() > 0)
                                    @foreach($comentario->respostas as $resposta)
                                        @php $idSub = $resposta->id_comentario ?? $resposta->id; @endphp
                                        <div class="comentario subcomentario" id="comentario-{{ $idSub }}" style="margin-top: 10px; display: flex; gap: 10px;">
                                            <a href="{{ route('perfil.exibir', $resposta->usuario->nome_usuario) }}">
                                                <img src="{{ $resposta->usuario->perfil && $resposta->usuario->perfil->foto ? asset('storage/'.$resposta->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" class="avatar-comentario" style="width: 28px; height: 28px;">
                                            </a>
                                            <div class="corpo-comentario" style="width: 100%;">
                                                <div class="topo-comentario" style="display: flex; justify-content: space-between; align-items: center;">
                                                    <a href="{{ route('perfil.exibir', $resposta->usuario->nome_usuario) }}" style="text-decoration: none; color: inherit;">
                                                        <strong>{{ $resposta->usuario->nome_usuario }}</strong>
                                                    </a>
                                                    <small style="color: #888; font-size: 11px;">{{ $resposta->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p style="margin: 4px 0 0;">{{ $resposta->conteudo }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

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