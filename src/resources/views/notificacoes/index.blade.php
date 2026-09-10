@extends('layouts.app')

@section('titulo', 'Notificações')

@push('estilos')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .container-notificacoes-page {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            padding: 10px 0 40px;
        }

        .card-notificacoes {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0edf5;
            overflow: hidden;
        }

        .cabecalho-notificacoes-page {
            padding: 22px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0edf5;
            flex-wrap: wrap;
            gap: 16px;
        }

        .cabecalho-notificacoes-page h2 {
            font-size: 20px;
            color: #2b1740;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .acoes-notificacoes-topo {
            display: flex;
            gap: 10px;
        }

        .btn-acao-notif {
            background: #f4effa;
            color: #4c2b75;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-acao-notif:hover {
            background: #e9e0f5;
            color: #4c2b75;
        }

        .abas-filtro-notif {
            display: flex;
            border-bottom: 1px solid #f0edf5;
            background: #faf8fd;
            padding: 0 28px;
        }

        .aba-filtro-item {
            padding: 14px 20px;
            text-decoration: none;
            color: #726b80;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .aba-filtro-item.ativa {
            color: #ff7a45;
            font-weight: 600;
        }

        .aba-filtro-item.ativa::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #ff7a45;
            border-radius: 3px 3px 0 0;
        }

        .lista-notificacoes-detalhada {
            display: flex;
            flex-direction: column;
        }

        .item-notificacao-linha {
            display: flex;
            align-items: center;
            padding: 16px 28px;
            border-bottom: 1px solid #f6f3fa;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s ease;
            gap: 16px;
        }

        .item-notificacao-linha:hover {
            background: #faf7fd;
        }

        .item-notificacao-linha.nao-lida {
            background: #fbf7ff;
            border-left: 4px solid #ff7a45;
        }

        .avatar-notificacao-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .avatar-notificacao-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .badge-icone-tipo {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #ffffff;
            border: 2px solid #ffffff;
        }

        .tipo-curtida { background: #e0245e; }
        .tipo-comentario { background: #0084ff; }
        .tipo-seguidor { background: #4c2b75; }
        .tipo-compartilhamento { background: #059669; }
        .tipo-geral { background: #ff7a45; }

        .corpo-notificacao-linha {
            flex: 1;
            min-width: 0;
        }

        .texto-notificacao-principal {
            font-size: 14px;
            color: #2b1740;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .texto-notificacao-principal strong {
            color: #1a0c29;
        }

        .tempo-notificacao {
            font-size: 12px;
            color: #928a9f;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .indicador-nao-lida-ponto {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ff7a45;
            flex-shrink: 0;
        }

        .estado-vazio-notificacoes {
            text-align: center;
            padding: 50px 20px;
            color: #8c859c;
        }

        .estado-vazio-notificacoes i {
            font-size: 48px;
            color: #d4cde2;
            margin-bottom: 12px;
        }

        .paginacao-notificacoes {
            padding: 20px 28px;
            display: flex;
            justify-content: center;
        }

        /* MODO ESCURO */
        body.dark-mode .card-notificacoes {
            background: #1c162b;
            border-color: #2b223f;
        }

        body.dark-mode .cabecalho-notificacoes-page,
        body.dark-mode .abas-filtro-notif,
        body.dark-mode .item-notificacao-linha {
            border-color: #2b223f;
        }

        body.dark-mode .cabecalho-notificacoes-page h2,
        body.dark-mode .texto-notificacao-principal strong {
            color: #f1edfa;
        }

        body.dark-mode .texto-notificacao-principal {
            color: #dcd6e8;
        }

        body.dark-mode .abas-filtro-notif {
            background: #171224;
        }

        body.dark-mode .item-notificacao-linha:hover {
            background: #251d38;
        }

        body.dark-mode .item-notificacao-linha.nao-lida {
            background: #231936;
        }

        body.dark-mode .btn-acao-notif {
            background: #2c2340;
            color: #d1bdf0;
        }
    </style>
@endpush

@section('conteudo')
<div class="container-notificacoes-page">

    <div class="card-notificacoes">
        <!-- TOPO -->
        <div class="cabecalho-notificacoes-page">
            <h2>
                <i class="bi bi-bell-fill" style="color: #ff7a45;"></i>
                Notificações
            </h2>

            <div class="acoes-notificacoes-topo">
                <form action="{{ route('notificacoes.marcarLidas') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-acao-notif">
                        <i class="bi bi-check2-all"></i> Marcar todas como lidas
                    </button>
                </form>

                <form action="{{ route('notificacoes.limpar') }}" method="POST" style="display: inline;" onsubmit="return confirm('Deseja apagar as notificações lidas?')">
                    @csrf
                    <button type="submit" class="btn-acao-notif" style="color: #928a9f;">
                        <i class="bi bi-trash"></i> Limpar lidas
                    </button>
                </form>
            </div>
        </div>

        <!-- ABAS DE FILTRO -->
        <div class="abas-filtro-notif">
            <a href="{{ route('notificacoes.listar', ['filtro' => 'todas']) }}" 
               class="aba-filtro-item {{ $filtro === 'todas' ? 'ativa' : '' }}">
                Todas
            </a>
            <a href="{{ route('notificacoes.listar', ['filtro' => 'nao-lidas']) }}" 
               class="aba-filtro-item {{ $filtro === 'nao-lidas' ? 'ativa' : '' }}">
                Não Lidas ({{ auth()->user()->unreadNotifications->count() }})
            </a>
        </div>

        <!-- LISTAGEM -->
        <div class="lista-notificacoes-detalhada">
            @forelse($notificacoes as $n)
                @php
                    $ehNaoLida = is_null($n->read_at);
                    $dados = $n->data;
                    $tipo = $dados['tipo'] ?? 'geral';
                    $url = route('notificacoes.ler', $n->id);

                    $icone = match($tipo) {
                        'curtida' => 'bi-heart-fill',
                        'comentario' => 'bi-chat-fill',
                        'seguidor' => 'bi-person-plus-fill',
                        'compartilhamento' => 'bi-share-fill',
                        default => 'bi-bell-fill'
                    };

                    $classeTipo = match($tipo) {
                        'curtida' => 'tipo-curtida',
                        'comentario' => 'tipo-comentario',
                        'seguidor' => 'tipo-seguidor',
                        'compartilhamento' => 'tipo-compartilhamento',
                        default => 'tipo-geral'
                    };
                @endphp

                <a href="{{ $url }}" class="item-notificacao-linha {{ $ehNaoLida ? 'nao-lida' : '' }}">
                    <div class="avatar-notificacao-wrapper">
                        <img src="{{ !empty($dados['autor_foto']) ? asset('storage/' . $dados['autor_foto']) : asset('imagens/perfil-v1.png') }}" 
                             alt="{{ $dados['autor_nome'] ?? 'Autor' }}" 
                             class="avatar-notificacao-img">
                        <span class="badge-icone-tipo {{ $classeTipo }}">
                            <i class="bi {{ $icone }}"></i>
                        </span>
                    </div>

                    <div class="corpo-notificacao-linha">
                        <div class="texto-notificacao-principal">
                            <strong>{{ $dados['autor_nome'] ?? 'Alguém' }}</strong>
                            {{ $dados['mensagem'] ?? 'interagiu com você.' }}
                        </div>
                        <span class="tempo-notificacao">
                            <i class="bi bi-clock"></i>
                            {{ $n->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if($ehNaoLida)
                        <span class="indicador-nao-lida-ponto" title="Não lida"></span>
                    @endif
                </a>
            @empty
                <div class="estado-vazio-notificacoes">
                    <i class="bi bi-bell-slash"></i>
                    <h3>Nenhuma notificação por enquanto</h3>
                    <p>Quando alguém curtir, comentar ou compartilhar suas histórias, você verá aqui!</p>
                </div>
            @endforelse
        </div>

        @if($notificacoes->hasPages())
            <div class="paginacao-notificacoes">
                {{ $notificacoes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
