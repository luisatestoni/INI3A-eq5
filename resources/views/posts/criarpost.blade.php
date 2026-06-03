@extends('layouts.app')

@section('conteudo_interno')
<div class="painel-criar-publicacao">
    
    <a href="{{ route('feed') }}" class="botao-fechar-formulario">✕</a>
    
    <h2 class="titulo-formulario-post">Nova publicação</h2>

    <form action="{{ route('post.salvar') }}" method="POST" enctype="multipart/form-data" class="formulario-publicar-corpo">
        @csrf

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Título</label>
            <input type="text" name="titulo" placeholder="Título da sua publicação" maxlength="100" class="campo-texto borda-pessego">
            <div class="contador-caracteres">0/100</div>
        </div>

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Resumo</label>
            <textarea name="resumo" placeholder="Escreva um resumo..." maxlength="200" rows="2" class="campo-texto borda-pessego area-texto-redimensionável"></textarea>
            <div class="contador-caracteres">0/200</div>
        </div>

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Conteúdo</label>
            <textarea name="conteudo" placeholder="Comece a escrever..." maxlength="50000" rows="6" class="campo-texto borda-pessego area-texto-redimensionável"></textarea>
            <div class="contador-caracteres">0/50000</div>
        </div>

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Podcast <span class="opcional-texto">(opcional)</span></label>
            <label class="upload-arquivo-container">
                <span>🎙️</span> Adicionar um arquivo de Áudio
                <input type="file" name="podcast" accept="audio/*" class="escondido-input">
            </label>
        </div>

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Adicione uma capa <span class="opcional-texto">(opcional)</span></label>
            <label class="upload-arquivo-container">
                Adicionar uma Imagem
                <input type="file" name="capa" accept="image/*" class="escondido-input">
            </label>
        </div>

        <div class="grupo-campo-form">
            <label class="rotulo-campo">Categorias</label>
            <div class="seletor-categoria-falso">
                <span>Escolha até 3 categorias</span>
                <span class="seta-seletor">→</span>
            </div>
        </div>

        <div class="alinhamento-final-botao">
            <button type="submit" class="botao-roxo">Publicar</button>
        </div>
    </form>
</div>
@endsection