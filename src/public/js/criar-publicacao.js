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