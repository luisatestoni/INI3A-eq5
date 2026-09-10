@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/sobre.css') }}">
@endpush

@section('titulo', 'Sobre nós')

@section('conteudo')
<div class="container-sobre">
    <!-- Hero Section / Banner Inicial -->
    <section class="hero-sobre">
        <span class="tag-destaque">Conheça o Scribo</span>
        <h1 class="titulo-hero">Onde histórias ganham vida e ideias se conectam</h1>
        <p class="subtitulo-hero">
            O Scribo é uma plataforma feita para quem ama escrever, ler e compartilhar conhecimento de forma simples, moderna e sem distrações.
        </p>
    </section>

    <!-- Cards dos Pilares / Propósitos -->
    <section class="grade-pilares">
        <div class="card-pilar">
            <div class="icone-pilar">
                <i class="bi bi-pencil-square"></i>
            </div>
            <h3>Crie & Compartilhe</h3>
            <p>Sua voz importa. Publique textos, reflexões e conteúdos no seu tempo, com uma interface limpa que destaca o que realmente interessa.</p>
        </div>

        <div class="card-pilar">
            <div class="icone-pilar">
                <i class="bi bi-compass"></i>
            </div>
            <h3>Explore Ideias</h3>
            <p>Descubra novas perspectivas todos os dias. Navegue por assuntos em alta, tendências e opiniões de autores de toda a comunidade.</p>
        </div>

        <div class="card-pilar">
            <div class="icone-pilar">
                <i class="bi bi-people"></i>
            </div>
            <h3>Conecte-se</h3>
            <p>Encontre leitores e criadores apaixonados pelos mesmos temas que você. Construa uma rede de trocas valiosas e autênticas.</p>
        </div>
    </section>

    <!-- Seção Sobre a Equipe / Projeto -->
    <section class="secao-historia">
        <div class="conteudo-historia">
            <h2>Por que criamos o Scribo?</h2>
            <p>
                Em um ecossistema digital cheio de ruídos e interações superficiais, o **Scribo** nasceu com o objetivo de resgatar o valor das palavras e das conversas significativas.
            </p>
            <p>
                Desenvolvido com foco na experiência do usuário, combinamos simplicidade visual, velocidade e segurança para entregar a melhor plataforma de publicação e interação.
            </p>
        </div>
    </section>

    <!-- Call to Action (Chamada para Ação) -->
    <!-- Call to Action (Chamada para Ação) -->
    <section class="painel-cta">
        <h2>Pronto para começar sua jornada?</h2>
        <p>Junte-se à comunidade do Scribo e comece a compartilhar suas histórias hoje mesmo.</p>
        <div class="acoes-cta">
            @guest
                <a href="{{ route('cadastro') }}" class="btn-cta-principal">Criar Conta Gratuita</a>
                <a href="{{ route('feed') }}" class="btn-cta-outline">Explorar o Feed</a>
            @else
                <a href="{{ route('feed') }}" class="btn-cta-principal">Ir para o Feed</a>
            @endguest
        </div>
    </section>
</div>
@endsection