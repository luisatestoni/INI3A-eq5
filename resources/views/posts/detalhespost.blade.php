@extends('layouts.app')

@section('conteudo_interno')
<div class="painel-post-completo">
    
    <div class="cabecalho-autor-post">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80" class="avatar-autor-completo">
        <div class="textos-autor-headers">
            <p class="username-autor-completo">{{ $publicacao->usuario->usuario }}</p>
            <p class="data-post-completo">{{ $publicacao->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="banner-imagem-post">
        @if($publicacao->capa)
            <img src="{{ asset('storage/' . $publicacao->capa) }}" class="foto-banner-full">
        @else
            <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=800&q=80" class="foto-banner-full">
        @endif
    </div>

    <div class="artigo-conteudo-bloco">
        <h2 class="titulo-artigo-principal">{{ $publicacao->titulo }}</h2>
        <div class="corpo-texto-artigo">
            {!! nl2br(e($publicacao->conteudo)) !!}
        </div>
    </div>

    <div class="barra-interacoes-rodape">
        <button class="item-interacao">❤️ 124</button>
        <button class="item-interacao">💬 124</button>
        <button class="item-interacao">🔖 Salvar</button>
        <button class="item-interacao">📤 Compartilhar</button>
    </div>

    <div class="secao-comentarios-posts">
        <h4 class="titulo-comentarios">Comentários</h4>
        
        <div class="caixa-criar-comentario">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" class="avatar-comentador">
            <div class="formulario-comentario-linha">
                <input type="text" placeholder="Escreva um comentário..." class="campo-texto input-arredondado">
                <button class="botao-enviar-comentario">Publicar</button>
            </div>
        </div>

        <div class="item-comentario-completado">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=80&q=80" class="avatar-comentador">
            <div class="textos-comentario-bloco">
                <p class="usuario-comentario-nome">simone_silva</p>
                <p class="texto-comentario-conteudo">Amei demais!! 💜</p>
                <div class="acoes-comentario-subtext">
                    <button class="link-resposta">Responder</button>
                    <button class="link-curtida-comentario">❤️ 124</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection