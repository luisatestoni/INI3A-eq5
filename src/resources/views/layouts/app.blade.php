<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('imagens/simbolo-v1.svg') }}">
    <title>Scribo - @yield('titulo', 'Rede Social')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}"> 

    <!-- Script rápido para prevenir FOUC (flash de modo claro) no carregamento -->
    <script>
        (function() {
            if (localStorage.getItem('scribo_dark_mode') === 'true') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    @stack('estilos')
</head>

<body>
    <script>
        if (localStorage.getItem('scribo_dark_mode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <header class="cabecalho-principal">
        <div class="container-cabecalho">
            <div class="bloco-esquerda"> 
                <button class="botao-icone" id="btn-toggle-darkmode" title="Alternar Modo Escuro/Claro"> 
                    <i class="fa-regular fa-moon" id="icone-darkmode"></i> 
                </button> 
            </div>

            <div class="bloco-centro"> 
                <a href="{{ route('feed') }}" title="Página Inicial do Scribo"> 
                    <img src="{{ asset('imagens/logotipo-scribo-v1.svg') }}" alt="Scribo" class="imagem-logo"> 
                </a> 
            </div>

            <div class="bloco-direita"> 
                @auth
                    <!-- Sino com Contador e Dropdown de Notificações -->
                    <div class="container-notificacao-header">
                        @php $totalNaoLidas = auth()->user()->unreadNotifications->count(); @endphp
                        <button type="button" class="botao-icone" id="btn-notificacoes-header" title="Notificações">
                            <i class="fa-regular fa-bell"></i>
                            <span id="badge-notificacoes-count" class="badge-notificacao-header" @style(['display: none;' => ($totalNaoLidas ?? 0) <= 0])>                                    {{ $totalNaoLidas ?? 0 }}
                        </button>

                        <!-- Menu Dropdown de Notificações -->
                        <div id="dropdown-notificacoes-header" class="dropdown-notificacoes-header">
                            <div class="topo-drop-notif">
                                <h4>Notificações</h4>
                                <button type="button" id="btn-marcar-todas-lidas-header" class="btn-link-marcar-lidas">
                                    Marcar lidas
                                </button>
                            </div>

                            <div id="lista-dropdown-notificacoes" class="lista-drop-notif">
                                <div class="carregando-notif">
                                    <i class="bi bi-arrow-clockwise girando"></i> Carregando...
                                </div>
                            </div>

                            <div class="rodape-drop-notif">
                                <a href="{{ route('notificacoes.listar') }}">Ver todas as notificações</a>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar com Menu Dropdown do Usuário -->
                    <div class="container-usuario-header">
                        <button type="button" class="btn-avatar-header" id="btn-menu-usuario" title="{{ auth()->user()->nome }}">
                            @if(auth()->user()->perfil && auth()->user()->perfil->foto) 
                                <img src="{{ asset('storage/' . auth()->user()->perfil->foto) }}" alt="{{ auth()->user()->nome }}" class="avatar-usuario"> 
                            @else 
                                <div class="icone-perfil"> 
                                    <img class="icone-perfil-imagem" src="{{ asset('imagens/perfil-v1.png') }}" alt="Avatar Padrão"> 
                                </div> 
                            @endif
                            <i class="bi bi-chevron-down icone-seta-usuario"></i>
                        </button>

                        <!-- Menu Dropdown do Usuário -->
                        <div id="dropdown-menu-usuario" class="dropdown-menu-usuario">
                            <div class="info-usuario-drop">
                                <strong>{{ auth()->user()->nome }}</strong>
                                <span>{{ '@' . (auth()->user()->nome_usuario ?? 'usuario') }}</span>
                            </div>
                            <div class="divisor-menu-usuario"></div>
                            
                            <a href="{{ route('perfil.exibir', auth()->user()->nome_usuario) }}" class="item-menu-usuario">
                                <i class="bi bi-person-circle"></i> Meu Perfil
                            </a>

                            <a href="{{ route('metricas') }}" class="item-menu-usuario">
                                <i class="bi bi-bar-chart-line-fill"></i> Métricas & Insights
                            </a>

                            <a href="{{ route('publicacao.criar') }}" class="item-menu-usuario">
                                <i class="bi bi-plus-circle-fill"></i> Nova Publicação
                            </a>

                            <a href="{{ route('notificacoes.listar') }}" class="item-menu-usuario">
                                <i class="bi bi-bell"></i> Notificações
                            </a>

                            <a href="{{ route('senha.alterar') }}" class="item-menu-usuario">
                                <i class="bi bi-shield-lock"></i> Alterar Senha
                            </a>

                            <div class="divisor-menu-usuario"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="item-menu-usuario btn-logout-usuario">
                                    <i class="bi bi-box-arrow-right"></i> Sair da conta
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Visitante não logado -->
                    @if(!request()->routeIs('login')) 
                        <a href="{{ route('login') }}" class="btn-login-cabecalho">Entrar</a> 
                    @endif 
                @endauth
            </div>
        </div>
    </header>

    <!-- Sub-abas do Feed para navegação entre categorias e algoritmos -->
        @if(Route::is('feed')) 
        @php
            $abaAtual = $aba ?? request('aba', 'para-voce');
        @endphp
        <nav class="abas-feed"> 
            <a href="{{ route('feed', ['aba' => 'para-voce']) }}" 
            class="item-aba {{ ($aba ?? request('aba')) === 'para-voce' || !request('aba') ? 'ativo' : '' }}">
                Para você
            </a> 
            <a href="{{ route('feed', ['aba' => 'seguindo']) }}" 
            class="item-aba {{ ($aba ?? request('aba')) === 'seguindo' ? 'ativo' : '' }}">
                Seguindo
            </a> 
            <a href="{{ route('feed', ['aba' => 'tendencias']) }}" 
            class="item-aba {{ ($aba ?? request('aba')) === 'tendencias' ? 'ativo' : '' }}">
                Tendências
            </a> 
        </nav>
    @endif

    <!-- Barra Lateral de Navegação -->
    @if(request()->routeIs('feed') || request()->routeIs('perfil.exibir') || request()->routeIs('explorar') || request()->routeIs('mensagens.index') || request()->routeIs('notificacoes.listar'))
        <aside class="barra-lateral">
            <a href="{{ route('feed') }}" class="link-aba {{ request()->routeIs('feed') ? 'ativo' : '' }}" title="Início">
                <i class="bi bi-house-door-fill"></i>
            </a>

            <a href="{{ route('explorar') }}" class="link-aba {{ request()->routeIs('explorar') ? 'ativo' : '' }}" title="Explorar Histórias & Autores">
                <i class="bi bi-compass"></i>
            </a>

            <a href="{{ route('publicacao.criar') }}" class="link-aba {{ request()->routeIs('publicacao.criar') ? 'ativo' : '' }}" title="Nova Publicação">
                <i class="bi bi-plus-circle-fill"></i>
            </a>

            @auth
            <a href="#" class="link-aba {{ request()->routeIs('mensagens.index') ? 'ativo' : '' }}" title="Mensagens">
                <i class="bi bi-chat-dots-fill"></i>
            </a>

            <a href="{{ route('notificacoes.listar') }}" class="link-aba {{ request()->routeIs('notificacoes.listar') ? 'ativo' : '' }}" title="Notificações">
                <i class="bi bi-bell-fill"></i>
            </a>
            @endauth
        </aside>
    @endif

    <main class="conteudo-principal-site {{ (request()->routeIs('feed') || request()->routeIs('perfil.exibir') || request()->routeIs('metricas') || request()->routeIs('notificacoes.listar')) ? 'com-barra-lateral' : '' }}"> 
        @yield('conteudo') 
    </main>

    <!-- Modal Universal de Compartilhamento -->
    @include('componentes.modal-compartilhar')

    <!-- Container Global de Toasts -->
    <div id="container-toasts-global" class="container-toasts-global"></div>

    <footer class="rodape-principal">
        <div class="container-rodape">
            <!-- Coluna 1: Branding e Redes -->
            <div class="coluna-rodape sobre">
                <img src="{{ asset('imagens/logotipo-scribo-v1.svg') }}" alt="Scribo" class="logo-rodape">
                <p class="descricao-rodape">Sua plataforma para compartilhar histórias, ideias e se conectar com pessoas de forma autêntica.</p>
                
                <div class="redes-sociais">
                    <a href="https://instagram.com/scribo.tcc" title="Instagram" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" title="GitHub" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>

            <!-- Coluna 2: Links Rápidos -->
            <div class="coluna-rodape">
                <h4 class="titulo-coluna">Navegação</h4>
                <ul class="links-rodape">
                    <li><a href="{{ route('inicial') }}">Início</a></li>
                    <li><a href="{{ route('explorar') }}">Explorar</a></li>
                    <li><a href="{{ route('sobre') }}">Sobre o Scribo</a></li>
                </ul>
            </div>

            <!-- Coluna 3: Suporte & Comunidade -->
            <div class="coluna-rodape">
                <h4 class="titulo-coluna">Contato</h4>
                <ul class="links-rodape">
                    <li><a href="#">scribo5.tcc@gmail.com</a></li>
                </ul>
            </div>
        </div>

        <!-- Sub-rodapé -->
        <div class="barrafinal-rodape">
            <p>&copy; 2026 Scribo. Todos os direitos reservados.</p>
            <p class="creditos">Desenvolvido pela equipe Scribo.</p>
        </div>
    </footer>

    <!-- Scripts Globais -->
    <script src="{{ asset('js/compartilhar.js') }}"></script>
    <script src="{{ asset('js/notificacoes.js') }}"></script>
    <script>
        // Gerenciamento de Dark Mode
        const btnDarkMode = document.getElementById('btn-toggle-darkmode');
        const iconeDarkMode = document.getElementById('icone-darkmode');

        function sincronizarIconeDark() {
            const ehDark = document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode');
            if (iconeDarkMode) {
                iconeDarkMode.className = ehDark ? 'fa-solid fa-sun' : 'fa-regular fa-moon';
                iconeDarkMode.style.color = ehDark ? '#ff914d' : 'white';
            }
        }
        sincronizarIconeDark();

        if (btnDarkMode) {
            btnDarkMode.addEventListener('click', function() {
                const ativo = document.body.classList.toggle('dark-mode');
                document.documentElement.classList.toggle('dark-mode', ativo);
                localStorage.setItem('scribo_dark_mode', ativo);
                sincronizarIconeDark();
            });
        }

        // Toggle do menu dropdown do usuário
        const btnMenuUsuario = document.getElementById('btn-menu-usuario');
        const dropdownMenuUsuario = document.getElementById('dropdown-menu-usuario');

        if (btnMenuUsuario && dropdownMenuUsuario) {
            btnMenuUsuario.addEventListener('click', function(e) {
                e.stopPropagation();
                // Fecha dropdown de notificações se aberto
                const dropNotif = document.getElementById('dropdown-notificacoes-header');
                if (dropNotif) dropNotif.classList.remove('ativo');

                dropdownMenuUsuario.classList.toggle('ativo');
            });

            document.addEventListener('click', function(e) {
                if (!dropdownMenuUsuario.contains(e.target) && !btnMenuUsuario.contains(e.target)) {
                    dropdownMenuUsuario.classList.remove('ativo');
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>