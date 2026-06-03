<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribo</title>
    <link rel="stylesheet" href="{{ asset('css/componentes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <header class="topo-plataforma">
        <button class="botao-modo-noturno">🌙</button>

        <a href="{{ route('feed') }}" class="marca-central-dashboard">
            Scri<span>bo.</span>
        </a>

        <div class="usuario-notificacao-bloco">
            <button class="botao-notificacao">
                🔔 <span class="badge-contador">3</span>
            </button>
            <a href="{{ route('perfil.exibir', auth()->user()->usuario ?? 'luisa') }}" class="link-avatar-topo">
                <div class="moldura-avatar">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Perfil">
                </div>
            </a>
        </div>
    </header>

    <div class="container-plataforma">
        
        <aside class="menu-lateral">
            <a href="{{ route('feed') }}" class="link-menu ativo">🏠</a>
            <button class="link-menu">🔍</button>
            <button class="link-menu">🔖</button>
            <button class="link-menu">📬</button>
        </aside>

        <main class="conteudo-interno-dinamico">
            @yield('conteudo_interno')
        </main>
    </div>

    <a href="{{ route('post.criar') }}" class="botao-laranha-flutuante">+</a>

</body>
</html>