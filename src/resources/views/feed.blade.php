@extends('layouts.app')

@section('titulo', 'Feed Principal')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/feed.css') }}">
@endpush

@section('conteudo')
<div class="container-feed">

    <main class="conteudo-principal">

        <div class="topo-feed-navegacao">

            <button type="button" class="btn-filtrar" onclick="abrirModalFiltros()">
                <i class="bi bi-sliders"></i> Filtrar
            </button>
        </div>

        <!-- LISTA DE POSTS -->
        @forelse($publicacoes as $post)
            @php
                $palavras = str_word_count(strip_tags($post->conteudo ?? ''));
                $minutosLeitura = max(1, ceil($palavras / 180));
            @endphp

            <article class="cartao-post">

                @if($post->categorias)
                    <div class="categorias-post">
                        @foreach(explode(',', $post->categorias) as $categoria)
                            <a href="{{ route('feed', ['aba' => $aba ?? 'para-voce', 'categoria' => trim($categoria)]) }}" 
                               class="categoria-badge">
                                {{ trim($categoria) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="cabecalho-post">
                    <a href="{{ route('perfil.exibir', $post->usuario->nome_usuario) }}" class="link-autor">
                        <img src="{{ $post->usuario->perfil && $post->usuario->perfil->foto ? asset('storage/' . $post->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                             class="foto-autor" 
                             alt="{{ $post->usuario->nome_usuario }}">
                    </a>
                    
                    <div class="meta-cabecalho-post">
                        <div class="info-autor">
                            <a href="{{ route('perfil.exibir', $post->usuario->nome_usuario) }}" class="link-nome-autor">
                                <h4>{{ $post->usuario->nome_usuario }}</h4>
                            </a>
                            <span>{{ $post->data_publicacao->diffForHumans() }}</span>
                        </div>

                        
                    </div>
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
                        <img src="{{ asset('storage/' . $post->capa) }}" class="imagem-capa-post" alt="{{ $post->titulo }}">
                    @endif
                </div>

                <div class="acoes-post">
                    <!-- CURTIR -->
                    @php
                        $ja_curtiu = Auth::check() ? $post->curtidas->where('fk_id_usuario', Auth::id())->first() : null;
                    @endphp
                    <button type="button" 
                            class="botao-acao btn-curtir {{ $ja_curtiu ? 'curtido' : '' }}" 
                            data-id="{{ $post->id_publicacao }}"
                            data-token="{{ csrf_token() }}"
                            onclick="alternarCurtida(this)">
                        <i class="bi {{ $ja_curtiu ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        <span class="contador-curtidas">{{ $post->curtidas->count() }}</span>
                    </button>

                    <!-- COMENTAR -->
                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}#comentarios" 
                       class="botao-acao link-comentario">
                        <i class="bi bi-chat"></i>
                        <span>{{ $post->comentarios->count() }}</span>
                    </a>

                    <!-- SALVAR -->
                    @php
                        $ja_salvou = Auth::check() ? $post->salvos->where('fk_id_usuario', Auth::id())->first() : null;
                    @endphp
                    <button type="button" 
                            class="botao-acao btn-salvar {{ $ja_salvou ? 'salvo' : '' }}" 
                            data-id="{{ $post->id_publicacao }}" 
                            data-token="{{ csrf_token() }}" 
                            onclick="alternarSalvar(this)">
                        <i class="bi {{ $ja_salvou ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                        <span class="contador-salvos">{{ $post->salvos->count() }}</span>
                    </button>

                    <!-- COMPARTILHAR -->
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

            </article>
        @empty
            <div class="estado-vazio-feed">
                <i class="bi bi-journal-x"></i>
                <h3>Nenhuma publicação encontrada</h3>
                <p>Ajuste os filtros ou compartilhe uma nova história com a comunidade!</p>
            </div>
        @endforelse
    </main>
</div>

<!-- MODAL OVERLAY DE FILTROS (ESTILO APP) -->
<div id="modalFiltros" class="modal-overlay-filtros" onclick="fecharModalFiltrosFora(event)">
    <div class="modal-conteudo-filtros">
        <div class="cabecalho-modal-filtros">
            <h3>Filtrar Publicações</h3>
            <button class="btn-fechar-modal" onclick="fecharModalFiltros()">&times;</button>
        </div>

        <div class="secao-filtro-bloco">
            <h4>Ordenar por</h4>
            <a href="{{ route('feed', array_merge(request()->query(), ['ordem' => 'recentes'])) }}" class="opcao-ordenar">
                <i class="bi bi-clock-history"></i> Publicados mais recentemente
            </a>
        </div>

        @if(isset($categorias) && $categorias->isNotEmpty())
            <div class="secao-filtro-bloco">
                <h4>Categorias</h4>
                <div class="grid-categorias-modal">
                    <a href="{{ route('feed', ['aba' => $aba ?? 'para-voce']) }}" 
                       class="chip-categoria-modal {{ empty($categoriaSelecionada) ? 'ativo' : '' }}">
                        Todas
                    </a>

                    @foreach($categorias as $cat)
                        <a href="{{ route('feed', ['aba' => $aba ?? 'para-voce', 'categoria' => $cat->nome]) }}" 
                           class="chip-categoria-modal {{ ($categoriaSelecionada ?? '') === $cat->nome ? 'ativo' : '' }}">
                            {{ $cat->nome }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<a href="{{ route('publicacao.criar') }}" class="botao-flutuante-criar" title="Nova Publicação">+</a>
@endsection

@push('scripts')
    <script src="{{ asset('js/detalhes.js') }}"></script>
    <script>
        function abrirModalFiltros() {
            document.getElementById('modalFiltros').classList.add('ativo');
        }

        function fecharModalFiltros() {
            document.getElementById('modalFiltros').classList.remove('ativo');
        }

        function fecharModalFiltrosFora(e) {
            if (e.target.id === 'modalFiltros') {
                fecharModalFiltros();
            }
        }
    </script>
@endpush