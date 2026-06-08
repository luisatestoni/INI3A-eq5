@extends('layouts.app')

@section('titulo', isset($post) ? 'Editar Publicação' : 'Nova Publicação')

@push('estilos')
    <link rel="stylesheet" href="{{ asset('css/publicacao.css') }}">
@endpush

@section('conteudo')
<div class="container-nova-publicacao">
    
    <a href="{{ isset($post) ? route('publicacao.detalhes', $post->id_publicacao) : route('feed') }}" class="botao-fechar-modal" title="Cancelar">&times;</a>

    <h3 class="titulo-central-modal">{{ isset($post) ? 'Editar publicação' : 'Nova publicação' }}</h3>

    <form 
        action="{{ isset($post) ? route('publicacao.atualizar', $post->id_publicacao) : route('publicacao.salvar') }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="formulario-publicacao">
        
        @csrf

        @if(isset($post))
            @method('PUT')
        @endif

        <div class="grupo-campo-publicacao">
            <label for="titulo">Título</label>
            <div class="container-input-contador">
                <input 
                    type="text" 
                    id="titulo" 
                    name="titulo" 
                    maxlength="100" 
                    placeholder="Título da sua publicação" 
                    required 
                    value="{{ old('titulo', $post->titulo ?? '') }}">
                <span class="contador-caracteres" id="cont-titulo">0/100</span>
            </div>
        </div>

        <div class="grupo-campo-publicacao">
            <label for="resumo">Resumo</label>
            <div class="container-input-contador">
                <textarea 
                    id="resumo" 
                    name="resumo" 
                    maxlength="200" 
                    placeholder="Escreva um resumo..." 
                    rows="2">{{ old('resumo', $post->resumo ?? '') }}</textarea>
                <span class="contador-caracteres" id="cont-resumo">0/200</span>
            </div>
        </div>

        <div class="grupo-campo-publicacao">
            <label for="conteudo">Conteúdo</label>
            <div class="container-input-contador">
                <textarea 
                    id="conteudo" 
                    name="conteudo" 
                    maxlength="50000" 
                    placeholder="Comece a escrever..." 
                    rows="8" 
                    required>{{ old('conteudo', $post->conteudo ?? '') }}</textarea>
                <span class="contador-caracteres" id="cont-conteudo">0/50000</span>
            </div>
        </div>

        <div class="grupo-campo-publicacao">
            <label for="podcast">Podcast <span class="opcional-tag">(opcional)</span></label>
            <label class="upload-falso-container" style="cursor: pointer;">
                <i class="fa-solid fa-microphone"></i>
                <span id="texto-podcast">
                    {{ isset($post) && $post->podcast ? 'Alterar arquivo de áudio: ' . basename($post->podcast) : 'Adicionar um arquivo de áudio' }}
                </span>
                <input type="file" id="podcast" name="podcast" accept="audio/*" style="display: none;" onchange="atualizarNomeAudio(this)">
            </label>
        </div>

        <div class="grupo-campo-publicacao">
            <label for="capa">Adicione uma capa <span class="opcional-tag">(opcional)</span></label>
            
            @if(isset($post) && $post->capa)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $post->capa) }}" style="max-height: 80px; border-radius: 4px; display: block; margin-bottom: 5px;">
                </div>
            @endif

            <label class="upload-falso-container" style="cursor: pointer;">
                <i class="fa-solid fa-image"></i>
                <span id="texto-capa">
                    {{ isset($post) && $post->capa ? 'Alterar imagem de capa' : 'Adicionar uma imagem' }}
                </span>
                <input type="file" id="capa" name="capa" accept="image/*" style="display: none;" onchange="atualizarNomeArquivo(this)">
            </label>
        </div>

        <div class="grupo-campo-publicacao" style="position: relative;">
            <label>Categorias</label>
            
            <input 
                type="hidden" 
                id="categorias_input" 
                name="categorias" 
                required 
                value="{{ old('categorias', $post->categorias ?? '') }}">

            <div class="container-input-seta" id="gatilho-categorias">
                <div class="tags-selecionadas" id="container-tags">
                    <span class="placeholder-falso">Escolha até 3 categorias</span>
                </div>
                <span class="seta-categorias">&darr;</span>
            </div>

            <div class="opcoes-categorias-dropdown" id="dropdown-categorias" style="display: none;">
                @foreach($categorias as $grupo => $itens)
                    <div class="categoria-grupo-titulo">{{ $grupo }}</div>
                    @foreach($itens as $item)
                        <div class="opcao-item" data-value="{{ $item->nome }}">{{ $item->nome }}</div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="barra-acoes-final">
            <button type="submit" class="botao-publicar-roxo">
                {{ isset($post) ? 'Salvar Alterações' : 'Publicar' }}
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // Atualizar os contadores de texto dinamicamente enquanto digita e na inicialização
    const inicializarContador = (idInput, idContador, max) => {
        const el = document.getElementById(idInput);
        const cont = document.getElementById(idContador);
        
        // Define o valor inicial (importante para quando a página abre com texto na Edição)
        cont.textContent = `${el.value.length}/${max}`;
        
        el.addEventListener('input', () => {
            cont.textContent = `${el.value.length}/${max}`;
        });
    };
    
    inicializarContador('titulo', 'cont-titulo', 100);
    inicializarContador('resumo', 'cont-resumo', 200);
    inicializarContador('conteudo', 'cont-conteudo', 50000);

    // Mostra o nome da imagem selecionada
    function atualizarNomeArquivo(input) {
        const texto = document.getElementById('texto-capa');
        if (input.files && input.files[0]) {
            texto.textContent = input.files[0].name;
            texto.style.color = '#4c2b75';
        }
    }

    // Mostra o nome do arquivo de áudio selecionado
    function atualizarNomeAudio(input) {
        const texto = document.getElementById('texto-podcast');
        if (input.files && input.files[0]) {
            texto.textContent = input.files[0].name;
            texto.style.color = '#4c2b75';
        }
    }

    // Lógica do Dropdown e Tags de Categoria
    const gatilho = document.getElementById('gatilho-categorias');
    const dropdown = document.getElementById('dropdown-categorias');
    const containerTags = document.getElementById('container-tags');
    const inputOculto = document.getElementById('categorias_input');
    const itens = document.querySelectorAll('.opcao-item');
    
    // Inicializa o array com o que já estiver salvo no banco (caso seja Edição)
    let selecionadas = [];
    if (inputOculto.value.trim() !== '') {
        selecionadas = inputOculto.value.split(',').map(cat => cat.trim());
    }

    // Abre/fecha o dropdown
    gatilho.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    // Fecha ao clicar fora
    document.addEventListener('click', (e) => {
        if (!gatilho.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Marca visualmente no dropdown as categorias que já vieram selecionadas do banco
    itens.forEach(item => {
        const valor = item.getAttribute('data-value');
        if (selecionadas.includes(valor)) {
            item.classList.add('selecionado');
        }

        item.addEventListener('click', () => {
            if (selecionadas.includes(valor)) {
                selecionadas = selecionadas.filter(cat => cat !== valor);
                item.classList.remove('selecionado');
            } else {
                if (selecionadas.length < 3) {
                    selecionadas.push(valor);
                    item.classList.add('selecionado');
                } else {
                    alert('Você pode selecionar no máximo 3 categorias.');
                }
            }
            atualizarInterface();
        });
    });

    function atualizarInterface() {
        containerTags.innerHTML = '';

        if (selecionadas.length === 0) {
            containerTags.innerHTML = '<span class="placeholder-falso">Escolha até 3 categorias</span>';
            inputOculto.value = '';
            return;
        }

        selecionadas.forEach(cat => {
            const tag = document.createElement('span');
            tag.className = 'tag-badge';
            tag.textContent = cat;
            containerTags.appendChild(tag);
        });

        inputOculto.value = selecionadas.join(', ');
    }

    // Executa uma vez na inicialização para montar as tags salvas se for edição
    atualizarInterface();
</script>
@endpush