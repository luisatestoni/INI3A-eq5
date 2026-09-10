@extends('layouts.app')

@section('titulo', 'Métricas & Desempenho')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/metricas.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('conteudo')
<div class="container-metricas">

    <!-- CABEÇALHO DA DASHBOARD -->
    <header class="cabecalho-metricas">
        <div class="usuario-banner-metricas">
            <img src="{{ $usuario->perfil && $usuario->perfil->foto ? asset('storage/' . $usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                 alt="{{ $usuario->nome }}" 
                 class="avatar-metricas">
            <div class="titulos-metricas">
                <h2>Painel de Métricas & Insights</h2>
                <p>Acompanhe o alcance, engajamento e a evolução das suas histórias no Scribo.</p>
            </div>
        </div>

        <div class="acoes-cabecalho-metricas">
            <a href="{{ route('publicacao.criar') }}" class="btn-acao-criar">
                <i class="bi bi-plus-lg"></i> Nova Publicação
            </a>
            <a href="{{ route('perfil.exibir', $usuario->nome_usuario) }}" class="btn-acao-perfil">
                <i class="bi bi-person"></i> Ver Meu Perfil
            </a>
        </div>
    </header>

    <!-- GRID DE KPIS PRINCIPAIS -->
    <section class="grid-kpis">
        <!-- KPI: Total de Publicações -->
        <div class="card-kpi kpi-posts">
            <div class="icone-kpi">
                <i class="bi bi-journal-richtext"></i>
            </div>
            <div class="info-kpi">
                <span class="label-kpi">Publicações</span>
                <strong class="valor-kpi">{{ number_format($totalPosts, 0, ',', '.') }}</strong>
                <span class="sub-kpi">Histórias escritas</span>
            </div>
        </div>

        <!-- KPI: Total de Curtidas -->
        <div class="card-kpi kpi-curtidas">
            <div class="icone-kpi">
                <i class="bi bi-heart-fill"></i>
            </div>
            <div class="info-kpi">
                <span class="label-kpi">Curtidas Recebidas</span>
                <strong class="valor-kpi">{{ number_format($totalCurtidas, 0, ',', '.') }}</strong>
                <span class="sub-kpi">Aprovações de leitores</span>
            </div>
        </div>

        <!-- KPI: Comentários Recebidos -->
        <div class="card-kpi kpi-comentarios">
            <div class="icone-kpi">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
            <div class="info-kpi">
                <span class="label-kpi">Comentários</span>
                <strong class="valor-kpi">{{ number_format($totalComentarios, 0, ',', '.') }}</strong>
                <span class="sub-kpi">Interações em conversas</span>
            </div>
        </div>

        <!-- KPI: Compartilhamentos & Salvos -->
        <div class="card-kpi kpi-compartilhamentos">
            <div class="icone-kpi">
                <i class="bi bi-share-fill"></i>
            </div>
            <div class="info-kpi">
                <span class="label-kpi">Compartilhamentos</span>
                <strong class="valor-kpi">{{ number_format($totalCompartilhamentos, 0, ',', '.') }}</strong>
                <span class="sub-kpi">{{ $totalSalvos }} post(s) salvos</span>
            </div>
        </div>
    </section>

    <!-- RESUMO DE ALCANCE E COMUNIDADE -->
    <section class="barra-resumo-alcance">
        <div class="item-resumo">
            <i class="bi bi-people-fill"></i>
            <div>
                <strong>{{ $totalSeguidores }}</strong>
                <span>Seguidores</span>
            </div>
        </div>
        <div class="divisor-resumo"></div>
        <div class="item-resumo">
            <i class="bi bi-person-check-fill"></i>
            <div>
                <strong>{{ $totalSeguindo }}</strong>
                <span>Seguindo</span>
            </div>
        </div>
        <div class="divisor-resumo"></div>
        <div class="item-resumo">
            <i class="bi bi-lightning-charge-fill"></i>
            <div>
                <strong>{{ $totalEngajamento }}</strong>
                <span>Engajamento Total</span>
            </div>
        </div>
        <div class="divisor-resumo"></div>
        <div class="item-resumo">
            <i class="bi bi-graph-up-arrow"></i>
            <div>
                <strong>{{ $mediaEngajamentoPorPost }}</strong>
                <span>Média / História</span>
            </div>
        </div>
    </section>

    <!-- DUAS COLUNAS: TOP HISTÓRIAS E CATEGORIAS -->
    <div class="grid-detalhes-metricas">

        <!-- COLUNA 1: TOP HISTÓRIAS -->
        <section class="painel-card painel-top-posts">
            <div class="cabecalho-painel">
                <div class="titulo-com-icone">
                    <i class="bi bi-trophy-fill icone-ouro"></i>
                    <h3>Publicações com Maior Destaque</h3>
                </div>
                <span class="tag-contador">{{ $topPublicacoes->count() }} melhores</span>
            </div>

            @if($topPublicacoes->isEmpty())
                <div class="estado-vazio-painel">
                    <i class="bi bi-pen"></i>
                    <p>Você ainda não publicou nenhuma história.</p>
                    <a href="{{ route('publicacao.criar') }}" class="btn-vazio">Criar primeira história</a>
                </div>
            @else
                <div class="lista-top-posts">
                    @foreach($topPublicacoes as $index => $post)
                        <article class="item-top-post">
                            <div class="posicao-ranking">
                                <span>#{{ $index + 1 }}</span>
                            </div>
                            <div class="detalhes-top-post">
                                <h4>
                                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}">
                                        {{ $post->titulo }}
                                    </a>
                                </h4>
                                <div class="meta-top-post">
                                    <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($post->data_publicacao)->format('d/m/Y') }}</span>
                                    @if($post->categorias)
                                        <span class="badge-cat-mini">{{ explode(',', $post->categorias)[0] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="estatisticas-top-post">
                                <span title="Curtidas"><i class="bi bi-heart-fill"></i> {{ $post->curtidas_count }}</span>
                                <span title="Comentários"><i class="bi bi-chat-fill"></i> {{ $post->comentarios_count }}</span>
                                <span title="Compartilhamentos"><i class="bi bi-share-fill"></i> {{ $post->compartilhamentos ?? 0 }}</span>
                                <span title="Salvos"><i class="bi bi-bookmark-fill"></i> {{ $post->salvos_count }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- COLUNA 2: TEMAS E CATEGORIAS -->
        <section class="painel-card painel-categorias">
            <div class="cabecalho-painel">
                <div class="titulo-com-icone">
                    <i class="bi bi-tags-fill icone-categorias"></i>
                    <h3>Temas Mais Publicados</h3>
                </div>
                <span class="tag-contador">{{ count($categoriasPercentual) }} tópicos</span>
            </div>

            @if(empty($categoriasPercentual))
                <div class="estado-vazio-painel">
                    <i class="bi bi-tag"></i>
                    <p>Nenhuma categoria registrada em suas histórias.</p>
                </div>
            @else
                <div class="lista-barras-categorias">
                    @foreach($categoriasPercentual as $nome => $dados)
                        <div class="item-barra-categoria">
                            <div class="linha-info-categoria">
                                <span class="nome-cat">{{ $nome }}</span>
                                <span class="percentual-cat">{{ $dados['qtd'] }} post(s) ({{ $dados['porcentagem'] }}%)</span>
                            </div>
                            <div class="trilha-barra">
                            <div class="preenchimento-barra" @style(['width' => max($dados['porcentagem'] ?? 0, 5) . '%'])></div>                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    </div>

    <!-- SEÇÃO: HISTÓRICO DE ATIVIDADE (ÚLTIMOS 6 MESES) -->
    <section class="painel-card painel-historico">
        <div class="cabecalho-painel">
            <div class="titulo-com-icone">
                <i class="bi bi-bar-chart-line-fill icone-grafico"></i>
                <h3>Evolução de Atividade (Últimos 6 Meses)</h3>
            </div>
            <span class="tag-contador">Semestral</span>
        </div>

        <div class="grafico-barras-historico">
            @php
                $maxPosts = max(array_map(fn($item) => $item['posts'], $historicoMeses));
                $maxInteracoes = max(array_map(fn($item) => $item['curtidas'] + $item['comentarios'], $historicoMeses));
                $teto = max($maxPosts, $maxInteracoes, 1);
            @endphp

            @foreach($historicoMeses as $item)
                @php
                    $alturaPosts = round(($item['posts'] / $teto) * 120);
                    $totalInteracoes = $item['curtidas'] + $item['comentarios'];
                    $alturaInteracoes = round(($totalInteracoes / $teto) * 120);
                @endphp
                <div class="coluna-mes">
                    <div class="area-barras">
                    <div class="barra barra-posts" 
                        @style(['height' => max($alturaPosts ?? 0, 4) . 'px']) 
                        title="{{ $item['posts'] ?? 0 }} história(s)">
                    </div>                            @if($item['posts'] > 0)
                                <span class="valor-topo">{{ $item['posts'] }}</span>
                            @endif
                        </div>
                    <div class="barra barra-interacoes" 
                        @style(['height' => max($alturaInteracoes ?? 0, 4) . 'px']) 
                        title="{{ $totalInteracoes ?? 0 }} interações ({{ $item['curtidas'] ?? 0 }} curtidas, {{ $item['comentarios'] ?? 0 }} comentários)">
                    </div>                            @if($totalInteracoes > 0)
                                <span class="valor-topo">{{ $totalInteracoes }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="legenda-mes">{{ $item['mes'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="legenda-grafico">
            <div class="item-legenda">
                <span class="ponto-legenda cor-posts"></span>
                <span>Histórias Publicadas</span>
            </div>
            <div class="item-legenda">
                <span class="ponto-legenda cor-interacoes"></span>
                <span>Interações (Curtidas + Comentários)</span>
            </div>
        </div>
    </section>

    <!-- BANNER COMUNIDADE SCRIBO -->
    <div class="banner-comunidade-scribo">
        <div class="texto-comunidade">
            <h4><i class="bi bi-stars"></i> Comunidade Scribo em Crescimento</h4>
            <p>Junto com você, já somos <strong>{{ $totalMembrosComunidade }}</strong> escritores compartilhando mais de <strong>{{ $totalHistoriasComunidade }}</strong> publicações inspiradoras.</p>
        </div>
        <a href="{{ route('explorar') }}" class="btn-explorar-comunidade">
            Explorar Autores & Ideias
        </a>
    </div>

</div>
@endsection
