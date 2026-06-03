@extends('layouts.app')

@section('conteudo_interno')
<div class="painel-perfil-completo">
    
    <div class="banner-topo-perfil">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80" class="foto-capa-perfil">
    </div>

    <div class="bloco-infos-usuario-perfil">
        <div class="moldura-avatar-perfil">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80" class="foto-avatar-perfil">
        </div>

        <div class="acoes-perfil-direita">
            <button onclick="abrirModalSenha()" class="botao-roxo compacto">Editar Perfil</button>
            <a href="{{ route('post.criar') }}" class="botao-roxo compacto">Postar</a>
        </div>

        <div class="dados-texto-usuario-perfil">
            <div class="linha-username-contadores">
                <span class="username-perfil-destaque">{{ $dadosUsuario->nome }}</span>
                <span class="contador-item-perfil"><strong class="valor-num">130</strong> Seguidores</span>
                <span class="contador-item-perfil"><strong class="valor-num">130</strong> Seguindo</span>
            </div>
            
            <p class="bio-perfil-texto">{{ $dadosUsuario->perfil->bio ?? 'Sem biografia disponível.' }}</p>
        </div>

        <button class="botao-configuracoes-perfil">⚙️</button>
    </div>

    <div class="abas-navegacao border-top-only">
        <button class="aba-item ativa">Atividade</button>
        <button class="aba-item">Capítulos</button>
        <button class="aba-item">Curtidos</button>
    </div>

    <div class="feed-posts-proprios-perfil">
        <div class="input-falso-pensamento">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=50&q=80" class="miniatura-user-pensamento">
            <span>O que está pensando?</span>
        </div>

        @foreach($dadosUsuario->posts as $postProprio)
        <div class="card-postagem-destaque">
            <div class="dados-post-destaque-esquerdos">
                <div class="autor-linha-destaque">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=50&q=80" class="miniatura-user-pensamento">
                    <span class="subtext-destaque">{{ $dadosUsuario->usuario }} • {{ $postProprio->created_at->diffForHumans() }}</span>
                </div>
                <h4 class="titulo-post-destaque">{{ $postProprio->titulo }}</h4>
                
                <div class="links-interacao-destaque">
                    <a href="{{ route('post.exibir', $postProprio->id_post) }}" class="link-ver-mais-branco">Ver mais ➔</a>
                    <span>❤️ 124</span>
                    <span>💬 124</span>
                    <span>🔖 124</span>
                </div>
            </div>

            <div class="logo-marca-dagua">Scribo :)</div>
            <span class="tag-status-laranha">Artigo</span>
        </div>
        @endforeach
    </div>
</div>

<div id="modal-senha" class="tela-modal escondido">
    <div class="caixa-modal-conteudo-senha">
        <button onclick="fecharModalSenha()" class="botao-fechar-modal-x">✕</button>
        <h3 class="titulo-modal-senha">Redefinir senha</h3>
        <p class="subtitulo-modal-senha">Insira sua nova senha</p>

        <form action="{{ route('perfil.alterar_senha') }}" method="POST" class="formulario-senha-modal">
            @csrf
            <div class="grupo-campo">
                <label class="rotulo-campo">Senha atual</label>
                <input type="password" name="senha_atual" required class="campo-texto">
            </div>
            <div class="grupo-campo">
                <label class="rotulo-campo">Nova senha</label>
                <input type="password" name="nova_senha" required class="campo-texto">
            </div>
            <button type="submit" class="botao-roxo total-width">Salvar</button>
        </form>
    </div>
</div>

<script>
    function abrirModalSenha() {
        document.getElementById('modal-senha').style.display = 'flex';
    }
    document.getElementById('modal-senha').style.display = 'none';
    function fecharModalSenha() {
        document.getElementById('modal-senha').style.display = 'none';
    }
</script>
@endsection