@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('conteudo')
@php
    $ehMeuPerfil = Auth::check() && Auth::id() == $usuario->id_usuario;
@endphp

<div class="container-perfil">

   <div class="capa-perfil-banner">
        <img src="{{ $usuario->perfil && $usuario->perfil->capa ? asset('storage/'.$usuario->perfil->capa) : asset('imagens/capa-padrao.jpg') }}">
    </div>

    <div class="cabecalho-perfil">

        <img src="{{ $usuario->perfil && $usuario->perfil->foto ? asset('storage/' . $usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" class="avatar-perfil-imagem">

            <div class="info-perfil">

                <div class="linha-superior">

                    <h2>{{ $usuario->nome }}</h2>

                    <div class="estatisticas">
                        <a href="#" id="abrir-modal-seguidores" data-id="{{ $usuario->id_usuario }}" class="link-estatistica">
                            Seguidores <strong id="contador-seguidores">{{ $usuario->seguidores->count() }}</strong>
                        </a>
                        <a href="#" id="abrir-modal-seguindo" data-id="{{ $usuario->id_usuario }}" class="link-estatistica">
                            Seguindo <strong>{{ $usuario->seguindo->count() }}</strong>
                        </a>
                    </div>

                    <div class="acoes-perfil">

                        @if($ehMeuPerfil)

                            <button type="button" id="btn-abrir-editar" class="btn-perfil">
                                Editar Perfil
                            </button>

                            <a href="{{ route('publicacao.criar') }}" class="btn-perfil">
                                Postar
                            </a>

                        @else

                        @php
                            // Verifica se você já segue este perfil específico
                            $jaSegue = \App\Models\Seguidor::where('fk_id_seguidor', Auth::guard('web')->id())
                                                            ->where('fk_id_seguido', $usuario->id_usuario)
                                                            ->exists();
                        @endphp

                        <button type="button" 
                                id="btn-seguir" 
                                class="btn-perfil {{ $jaSegue ? 'seguindo' : '' }}" 
                                data-id="{{ $usuario->id_usuario }}"
                                data-token="{{ csrf_token() }}"
                                onclick="alternarSeguir(this)">
                            {{ $jaSegue ? 'Seguindo' : 'Seguir' }}
                        </button>

                        <button class="btn-perfil">
                            Mensagem
                        </button>

                        @endif

                    </div>

                </div>

                <p class="username">
                    {{ '@' . ($usuario->nome_usuario ?? 'usuario') }}
                </p>

                <p class="bio">
                    {{ $usuario->perfil->bio ?? 'Sem biografia.' }}
                </p>

            </div>
    </div>

    @if($ehMeuPerfil)
    <div class="config-perfil">
        <i class="bi bi-gear" id="abrir-config"></i>
    </div>
    @endif

    @if($ehMeuPerfil)
    <div class="popup-config" id="popup-config">

        <h3>Configurações</h3>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="item-config">
                <i class="bi bi-box-arrow-right"></i>
                Sair
                <i class="bi bi-chevron-right"></i>
            </button>
        </form>

        <button class="item-config" id="btn-excluir-conta">
            <i class="bi bi-trash"></i>
            Excluir conta
            <i class="bi bi-chevron-right"></i>
        </button>

        <a href="{{ route('senha.alterar') }}" class="item-config">
            <i class="bi bi-key"></i>
            Alterar senha
        </a>

        <button class="btn-cancelar-config" id="fechar-config">
            Cancelar
        </button>

    </div>

    @endif

    <div class="abas-perfil">
        
        <button class="aba ativa" data-tab="atividade">
            Atividade
        </button>

        <button class="aba" data-tab="capitulos">
            Capítulos
        </button>

        <button class="aba" data-tab="salvos">
            Salvos e curtidos
        </button>

    </div>

    @if($ehMeuPerfil)
    <div class="caixa-postagem">
        <input
            type="text"
            placeholder="O que está pensando?">
    </div>
    @endif

    <div class="lista-posts-perfil">

    @forelse($usuario->publicacoes as $post)

        <article class="cartao-post-perfil">

            @if($post->categorias)
                <div class="categorias-post-perfil">
                    @foreach(explode(',', $post->categorias) as $categoria)
                        <span class="categoria-badge-perfil">
                            {{ trim($categoria) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="cabecalho-post-perfil">

                <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" class="link-autor-perfil">
                    <img 
                        src="{{ $post->usuario->perfil && $post->usuario->perfil->foto ? asset('storage/' . $post->usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" 
                        class="foto-autor-perfil" 
                        alt="Avatar">
                </a>

                <div class="info-autor-perfil">
                    <a href="{{ route('perfil.exibir', $post->usuario->id_usuario) }}" class="link-nome-autor-perfil">
                        <h4>{{ $post->usuario->nome_usuario }}</h4>
                    </a>

                    <span>{{ $post->data_publicacao->diffForHumans() }}</span>
                </div>

            </div>

            <div class="corpo-post-perfil">

                <div class="texto-post-perfil">
                    <h3>
                        <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}">
                            {{ $post->titulo }}
                        </a>
                    </h3>

                    <p>{{ $post->resumo }}</p>

                    <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="link-ver-mais-perfil">
                        Ver mais
                    </a>
                </div>

                @if($post->capa)
                    <img 
                        src="{{ asset('storage/' . $post->capa) }}" 
                        class="imagem-capa-post-perfil" >
                @endif

            </div>

            <div class="acoes-post-perfil">

                @php
                    $ja_curtiu = $post->curtidas->where('fk_id_usuario', Auth::id())->first();
                @endphp

                <button type="button" 
                        class="botao-acao-perfil btn-curtir {{ $ja_curtiu ? 'curtido' : '' }}" 
                        data-id="{{ $post->id_publicacao }}"
                        data-token="{{ csrf_token() }}"
                        onclick="alternarCurtida(this)">
                    <i class="bi {{ $ja_curtiu ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    <span class="contador-curtidas">{{ $post->curtidas->count() }}</span>
                </button>

                <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="botao-acao-perfil">
                    <i class="bi bi-chat"></i>
                    <span>{{ $post->comentarios->count() }}</span>
                </a>

                <button type="button" class="botao-acao-perfil">
                    <i class="bi bi-bookmark"></i>
                </button>

                <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" class="botao-acao-perfil compartilhar-perfil">
                    <i class="bi bi-upload"></i>
                </a>

            </div>

        </article>

    @empty

        <p class="sem-postagens">
            Nenhuma publicação encontrada.
        </p>

    @endforelse

</div>

@if($ehMeuPerfil)
<div class="modal-overlay" id="modal-editar-perfil" style="display: none;">
    <div class="modal-container" style="max-width: 550px; width: 100%;">
        
        <div class="modal-cabecalho">
            <button type="button" class="btn-fechar-modal" id="btn-fechar-editar">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h2>Editar Perfil</h2>
            <div style="width: 24px;"></div>
        </div>

        <form action="{{ route('perfil.atualizar', $usuario->id_usuario) }}" method="POST" enctype="multipart/form-data">            
            @csrf
            
            <div class="imagens-modal-wrapper" style="position: relative; margin-bottom: 60px;">
                
                <div class="modal-capa-preview-container" style="width: 100%; height: 130px; border-radius: 12px; overflow: hidden; background-color: #f0f0f0; position: relative;">
                    <img src="{{ $usuario->perfil && $usuario->perfil->capa ? asset('storage/' . $usuario->perfil->capa) : asset('imagens/capa-padrao.jpg') }}" id="capa-preview" style="width: 100%; height: 100%; object-fit: cover;">
                    
                    <label for="capa_input" class="botao-upload-lapis" style="position: absolute; top: 12px; right: 12px; background: #fff; border: 1px solid #ffd8bf; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.15);" title="Mudar Capa">
                        <i class="fa-solid fa-pencil" style="font-size: 13px; color: #333;"></i>
                    </label>
                    <input type="file" id="capa_input" name="capa" accept="image/*" style="display: none;" onchange="previewCapa(this)">
                </div>

                <div class="container-avatar-upload" style="position: absolute; bottom: -45px; left: 24px; z-index: 10;">
                    <div class="avatar-preview-wrapper" style="position: relative; width: 90px; height: 90px;">
                        <img src="{{ $usuario->perfil && $usuario->perfil->foto ? asset('storage/' . $usuario->perfil->foto) : asset('imagens/perfil-v1.png') }}" id="avatar-preview" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" alt="Avatar">
                        
                        <label for="foto_input" class="botao-upload-lapis" style="position: absolute; bottom: 0; right: 0; background: #fff; border: 1px solid #ffd8bf; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.15);" title="Mudar Foto">
                            <i class="fa-solid fa-pencil" style="font-size: 11px; color: #333;"></i>
                        </label>
                        <input type="file" id="foto_input" name="foto" accept="image/*" style="display: none;" onchange="previewImagem(this)">
                    </div>
                </div>

            </div>

            <div class="grupo-campo-modal">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="{{ $usuario->nome }}" required>
            </div>

            <div class="grupo-campo-modal">
                <label for="nome_usuario">Nome de usuário</label>
                <input type="text" id="nome_usuario" name="nome_usuario" value="{{ $usuario->nome_usuario ?? '' }}" required>
            </div>

            <div class="grupo-campo-modal">
                <label for="biografia">Biografia</label>
                <textarea id="biografia" name="biografia" rows="4">{{ $usuario->perfil->bio ?? '' }}</textarea>
            </div>

            <div class="modal-acoes">
                <button type="submit" class="btn-aplicar-alteracoes">Aplicar</button>
            </div>
        </form>

    </div>
</div>
@endif

<div id="modal-conexoes" class="modal-conexoes-overlay">
    <div class="modal-conexoes-container">
        <div class="modal-conexoes-header">
            <h3 id="modal-conexoes-titulo">Usuários</h3>
            <button id="fechar-modal-conexoes" class="btn-fechar-modal-conexoes">&times;</button>
        </div>
        <div id="modal-conexoes-lista" class="modal-conexoes-corpo">
            </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/perfil.js') }}"></script> 
@endpush