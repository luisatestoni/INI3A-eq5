@extends('layouts.app')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('conteudo')
@php
    $ehMeuPerfil = Auth::check() && Auth::id() == $usuario->id_usuario;

    $fotoPerfil = $usuario->perfil && $usuario->perfil->foto
        ? asset('storage/' . $usuario->perfil->foto)
        : asset('imagens/perfil-v1.png');

    $capaPerfil = $usuario->perfil && $usuario->perfil->capa
        ? asset('storage/' . $usuario->perfil->capa)
        : asset('imagens/capa-padrao.jpg');

    $totalSeguidores = $usuario->seguidores ? $usuario->seguidores->count() : 0;
    $totalSeguindo = $usuario->seguindo ? $usuario->seguindo->count() : 0;

    $jaSegue = false;

    if (!$ehMeuPerfil && Auth::check()) {
        $jaSegue = \App\Models\Seguidor::where('fk_id_seguidor', Auth::id())
            ->where('fk_id_seguido', $usuario->id_usuario)
            ->exists();
    }
@endphp

<div class="container-perfil">

    <div class="capa-perfil-banner">
        <img src="{{ $capaPerfil }}" alt="Capa do perfil">
    </div>

    <div class="cabecalho-perfil">

        <img src="{{ $fotoPerfil }}" class="avatar-perfil-imagem" alt="Foto de perfil">

        <div class="info-perfil">

            <div class="linha-superior">

                <h2>{{ $usuario->nome }}</h2>

                <div class="estatisticas">
                    <a href="#" id="abrir-modal-seguidores" data-id="{{ $usuario->id_usuario }}" class="link-estatistica">
                        Seguidores <strong id="contador-seguidores">{{ $totalSeguidores }}</strong>
                    </a>

                    <a href="#" id="abrir-modal-seguindo" data-id="{{ $usuario->id_usuario }}" class="link-estatistica">
                        Seguindo <strong id="contador-seguindo">{{ $totalSeguindo }}</strong>
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

                        @auth
                            <button
                                type="button"
                                id="btn-seguir"
                                class="btn-perfil {{ $jaSegue ? 'seguindo' : '' }}"
                                data-id="{{ $usuario->id_usuario }}"
                                data-token="{{ csrf_token() }}"
                                onclick="alternarSeguir(this)">
                                {{ $jaSegue ? 'Seguindo' : 'Seguir' }}
                            </button>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="btn-perfil">
                                Seguir
                            </a>
                        @endguest

                        <button type="button" class="btn-perfil">
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

        <div class="popup-config" id="popup-config">

            <h3>Configurações</h3>

            <form action="{{ route('sair') }}" method="POST">
                @csrf

                <button type="submit" class="item-config">
                    <i class="bi bi-box-arrow-right"></i>
                    Sair
                    <i class="bi bi-chevron-right"></i>
                </button>
            </form>

            <form action="{{ route('perfil.excluir') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
                @csrf
                @method('DELETE')

                <button type="submit" class="item-config">
                    <i class="bi bi-trash"></i>
                    Excluir conta
                    <i class="bi bi-chevron-right"></i>
                </button>
            </form>

            <a href="{{ route('perfil.alterarSenha.form') }}" class="item-config">
                <i class="bi bi-key"></i>
                Alterar senha
                <i class="bi bi-chevron-right"></i>
            </a>

            <button type="button" class="btn-cancelar-config" id="fechar-config">
                Cancelar
            </button>

        </div>
    @endif

    <div class="abas-perfil">

        <button type="button" class="aba ativa" data-tab="atividade">
            Atividade
        </button>

        <button type="button" class="aba" data-tab="capitulos">
            Capítulos
        </button>

        <button type="button" class="aba" data-tab="salvos">
            Salvos e curtidos
        </button>

    </div>

    @if($ehMeuPerfil)
        <div class="caixa-postagem">
            <a href="{{ route('publicacao.criar') }}">
                <input type="text" placeholder="O que está pensando?" readonly>
            </a>
        </div>
    @endif

    <div class="lista-posts-perfil">

        @forelse($usuario->publicacoes as $post)

            <div class="card-publicacao" style="position: relative;">

                <div class="texto-post">
                    <h4>
                        <a href="{{ route('publicacao.detalhes', $post->id_publicacao) }}" style="color: inherit; text-decoration: none;">
                            {{ $post->titulo }}
                        </a>
                    </h4>

                    <p>{{ $post->resumo }}</p>
                </div>

                @if($post->capa)
                    <img src="{{ asset('storage/' . $post->capa) }}" class="mini-capa" alt="Capa da publicação">
                @endif

            </div>

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

                <form action="{{ route('perfil.atualizar') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="imagens-modal-wrapper" style="position: relative; margin-bottom: 60px;">

                        <div class="modal-capa-preview-container" style="width: 100%; height: 130px; border-radius: 12px; overflow: hidden; background-color: #f0f0f0; position: relative;">

                            <img
                                src="{{ $capaPerfil }}"
                                id="capa-preview"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                alt="Capa">

                            <label
                                for="capa_input"
                                class="botao-upload-lapis"
                                style="position: absolute; top: 12px; right: 12px; background: #fff; border: 1px solid #ffd8bf; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.15);"
                                title="Mudar Capa">
                                <i class="fa-solid fa-pencil" style="font-size: 13px; color: #333;"></i>
                            </label>

                            <input
                                type="file"
                                id="capa_input"
                                name="capa"
                                accept="image/*"
                                style="display: none;"
                                onchange="previewCapa(this)">
                        </div>

                        <div class="container-avatar-upload" style="position: absolute; bottom: -45px; left: 24px; z-index: 10;">
                            <div class="avatar-preview-wrapper" style="position: relative; width: 90px; height: 90px;">

                                <img
                                    src="{{ $fotoPerfil }}"
                                    id="avatar-preview"
                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                                    alt="Avatar">

                                <label
                                    for="foto_input"
                                    class="botao-upload-lapis"
                                    style="position: absolute; bottom: 0; right: 0; background: #fff; border: 1px solid #ffd8bf; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.15);"
                                    title="Mudar Foto">
                                    <i class="fa-solid fa-pencil" style="font-size: 11px; color: #333;"></i>
                                </label>

                                <input
                                    type="file"
                                    id="foto_input"
                                    name="foto"
                                    accept="image/*"
                                    style="display: none;"
                                    onchange="previewImagem(this)">
                            </div>
                        </div>

                    </div>

                    <div class="grupo-campo-modal">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $usuario->nome) }}" required>
                    </div>

                    <div class="grupo-campo-modal">
                        <label for="nome_usuario">Nome de usuário</label>
                        <input type="text" id="nome_usuario" name="nome_usuario" value="{{ old('nome_usuario', $usuario->nome_usuario) }}" required>
                    </div>

                    <div class="grupo-campo-modal">
                        <label for="biografia">Biografia</label>
                        <textarea id="biografia" name="biografia" rows="4">{{ old('biografia', $usuario->perfil->bio ?? '') }}</textarea>
                    </div>

                    <div class="modal-acoes">
                        <button type="submit" class="btn-aplicar-alteracoes">
                            Aplicar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <div id="modal-conexoes" class="modal-conexoes-overlay">
        <div class="modal-conexoes-container">

            <div class="modal-conexoes-header">
                <h3 id="modal-conexoes-titulo">Usuários</h3>

                <button type="button" id="fechar-modal-conexoes" class="btn-fechar-modal-conexoes">
                    &times;
                </button>
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