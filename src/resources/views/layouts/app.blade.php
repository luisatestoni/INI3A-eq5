<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('imagens/simbolo-v1.svg') }}">
    <title>Scribo - @yield('titulo', 'Rede Social')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">    <link rel="stylesheet" href="{{ asset('css/global.css') }}"> 
    @stack('estilos')
</head>

<body>
    <header class="cabecalho-principal">
        <div class="container-cabecalho">
            <div class="bloco-esquerda"> 
                <button class="botao-icone" title="Modo Escuro"> <i class="fa-regular fa-moon"></i> </button> 
            </div>


            <div class="bloco-centro"> 
                <a href="{{ route('feed') }}"> 
                    <img src="{{ asset('imagens/logotipo-scribo-v1.svg') }}" alt="Scribo" class="imagem-logo"> 
                </a> 
            </div>

            <div class="bloco-direita"> 
                @if(request()->routeIs('inicial') || request()->routeIs('login') || request()->routeIs('cadastro')) 
                    @if(!request()->routeIs('login')) 
                        <a href="{{ route('login') }}" class="btn-login-cabecalho">Entrar</a> 
                    @endif 
                @else 
                    <button class="botao-icone" title="Notificações"> <i class="fa-regular fa-bell"></i> </button> 
                    <a href="{{ route('perfil.exibir', ['id' => auth()->id() ?? 1]) }}" class="link-avatar"> 
                        @if(auth()->user() && auth()->user()->perfil && auth()->user()->perfil->foto) 
                            <img src="{{ asset('storage/' . auth()->user()->perfil->foto) }}" alt="Meu Perfil" class="avatar-usuario"> 
                        @else 
                            <div class="icone-perfil"> 
                                <img class="icone-perfil-imagem" src="{{ asset('imagens/perfil-v1.png') }}" alt="Avatar Padrão"> 
                            </div> 
                        @endif 
                    </a> 
                @endif 
            </div>
        </div>
    </header>
    @if(Route::is('feed')) 
        <nav class="abas-feed"> <a href="#" class="item-aba ativo">Para você</a> <a href="#" class="item-aba">Seguindo</a> <a href="#" class="item-aba">Tendências</a> <a href="#" class="item-aba">Recentes</a> </nav> @endif @if(request()->routeIs('feed') || request()->routeIs('perfil.exibir')) 
            <<!-- 1. Barra Lateral: adicione id="btn-explorar" e onclick para abrir -->
<aside class="barra-lateral">
    <a href="{{ route('feed') }}" class="link-aba {{ request()->routeIs('feed') ? 'ativo' : '' }}" title="Início">
        <i class="bi bi-house-door-fill"></i>
    </a>


    <a href="{{ route('explorar') }}" class="link-aba" title="Explorar"
        class="link-aba {{ request()->routeIs('explorar') ? 'ativo' : '' }}"
        title="Explorar">
        <i class="bi bi-compass"></i>
    </a>

    <a href="{{ route('publicacao.criar') }}" class="link-aba {{ request()->routeIs('publicacao.criar') ? 'ativo' : '' }}" title="Nova publicação">
        <i class="bi bi-plus-circle-fill"></i>
    </a>

    <a href="#" class="link-aba" title="Mensagens">
        <i class="bi bi-chat-dots"></i>
    </a>
</aside>

<!-- 2. Overlay / Modal de Pesquisa (Cole antes do </body>) -->
<div id="modal-pesquisa" class="modal-pesquisa">
    <div class="conteudo-modal-pesquisa">
        <button class="btn-fechar-pesquisa" onclick="fecharPesquisa()">&times;</button>
        <h3>Explorar e Pesquisar</h3>
        <form action="{{ route('feed') }}" method="GET" class="form-pesquisa-modal">
            <input type="text" id="campo-busca-input" name="busca" placeholder="Digite para buscar posts..." value="{{ request('busca') }}" autocomplete="off" required>
            <button type="submit" class="btn-buscar-modal">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
</div>

</aside>
 @endif <main class="conteudo-principal-site {{ (request()->routeIs('feed') || request()->routeIs('perfil.exibir')) ? 'com-barra-lateral' : '' }}"> @yield('conteudo') </main>
    <footer class="rodape-principal">
        <p>&copy; 2026 Scribo. Desenvolvido exatamente com base nos protótipos.</p>
    </footer> @stack('scripts')
</body>

</html> 