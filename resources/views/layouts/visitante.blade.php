<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scribo</title>
    <link rel="stylesheet" href="{{ asset('css/componentes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/autenticacao.css') }}">
</head>
<body>

    <header class="topo-visitante">
        @if(!Route::is('inicial'))
            <a href="javascript:history.back()" class="botao-voltar">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
        @else
            <div></div>
        @endif

        <div class="marca-scribo">
            Scri<span>bo.</span>
        </div>

        @if(Route::is('inicial'))
            <a href="{{ route('login') }}" class="botao-entrar-topo">
                Entrar <span>→</span>
            </a>
        @else
            <div></div>
        @endif
    </header>

    <main class="conteudo-principal">
        @yield('conteudo')
    </main>

</body>
</html>