// -------------------------------------------------------------------------
// FUNCIONALIDADE 1: ALTERNAR CURTIDA ASSÍNCRONA
// -------------------------------------------------------------------------
function alternarCurtida(botao) {
    const idPublicacao = botao.getAttribute('data-id');
    const tokenCsrf = botao.getAttribute('data-token');

    // Faz a requisição em segundo plano para o Laravel
    fetch(`/publicacao/${idPublicacao}/curtir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenCsrf,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            // Atualiza o contador de curtidas na tela na hora
            const contador = botao.querySelector('.contador-curtidas');
            contador.textContent = data.total_curtidas;

            // Muda o visual do ícone do coração dinamicamente
            const icone = botao.querySelector('i');
            if (data.curtido) {
                botao.classList.add('curtido');
                icone.className = 'bi bi-heart-fill';
            } else {
                botao.classList.remove('curtido');
                icone.className = 'bi bi-heart';
            }
        }
    })
    .catch(error => console.error('Erro ao curtir:', error));
}

// -------------------------------------------------------------------------
// FUNCIONALIDADE 2: ENVIAR COMENTÁRIO/RESPOSTA ASSÍNCRONO
// -------------------------------------------------------------------------
// -------------------------------------------------------------------------
// FUNCIONALIDADE 2: ENVIAR COMENTÁRIO/RESPOSTA ASSÍNCRONO
// -------------------------------------------------------------------------
function enviarComentarioAssincrono(event, formulario) {
    event.preventDefault(); // Impede o reload da página

    const url = formulario.action;
    const formData = new FormData(formulario);

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            // 1. Limpa o campo de texto
            const campoTexto = formulario.querySelector('input[name="conteudo"]') || formulario.querySelector('textarea[name="conteudo"]');
            if (campoTexto) campoTexto.value = '';

            // 2. Remove o aviso "Seja o primeiro a comentar" se existir
            const semComentarios = document.querySelector('.sem-comentarios');
            if (semComentarios) semComentarios.remove();

            // 3. Identifica se é resposta ou comentário principal
            const idPai = data.id_pai;
            if (idPai) {
                // --- É UMA RESPOSTA ---
                let containerRespostas = document.getElementById(`respostas-do-comentario-${idPai}`);

                // Se a div de respostas ainda não existir no comentário pai, cria dinamicamente
                if (!containerRespostas) {
                    const comentarioPai = document.getElementById(`comentario-${idPai}`);
                    if (comentarioPai) {
                        containerRespostas = document.createElement('div');
                        containerRespostas.id = `respostas-do-comentario-${idPai}`;
                        containerRespostas.className = 'respostas-comentario';
                        containerRespostas.style.cssText = 'margin-top: 10px; padding-left: 14px; border-left: 2px solid #ebe5f2;';
                        
                        const corpo = comentarioPai.querySelector('.corpo-comentario') || comentarioPai;
                        corpo.appendChild(containerRespostas);
                    }
                }

                const htmlResposta = `
                    <div class="comentario subcomentario" id="comentario-${data.id_comentario || data.id}" style="margin-top: 10px; display: flex; gap: 10px; animation: fadeIn 0.4s ease;">
                        <a href="${data.usuario_perfil_url}">
                            <img src="${data.usuario_foto}" class="avatar-comentario" style="width: 28px; height: 28px;">
                        </a>
                        <div class="corpo-comentario" style="width: 100%;">
                            <div class="topo-comentario" style="display: flex; justify-content: space-between; align-items: center;">
                                <a href="${data.usuario_perfil_url}" style="text-decoration: none; color: inherit;">
                                    <strong>${data.usuario_nome}</strong>
                                </a>
                                <small style="color: #888; font-size: 11px;">Agora mesmo</small>
                            </div>
                            <p style="margin: 4px 0 0;">${data.conteudo}</p>
                        </div>
                    </div>
                `;

                if (containerRespostas) {
                    containerRespostas.insertAdjacentHTML('beforeend', htmlResposta);
                }

                // Reseta visualmente a barra de "Respondendo a..."
                cancelarResposta();

            } else {
                // --- É UM COMENTÁRIO PRINCIPAL ---
                const idComentario = data.id_comentario || data.id;
                const containerComentarios = document.querySelector('.lista-comentarios');
                const novoHtml = `
                    <div class="comentario" id="comentario-${idComentario}" style="animation: fadeIn 0.4s ease;">
                        <a href="${data.usuario_perfil_url}">
                            <img src="${data.usuario_foto}" class="avatar-comentario">
                        </a>
                        <div class="corpo-comentario" style="width: 100%;">
                            <div class="topo-comentario" style="display: flex; justify-content: space-between; align-items: center;">
                                <a href="${data.usuario_perfil_url}" style="text-decoration: none; color: inherit;">
                                    <strong>${data.usuario_nome}</strong>
                                </a>
                                <small style="color: #888; font-size: 11px;">Agora mesmo</small>
                            </div>
                            <p style="margin: 4px 0 8px;">${data.conteudo}</p>
                            <button type="button" 
                                    class="btn-responder" 
                                    style="background: none; border: none; color: #ff7a45; font-size: 12px; font-weight: 600; cursor: pointer; padding: 0; display: inline-flex; align-items: center; gap: 4px;"
                                    onclick="prepararResposta(${idComentario}, '${data.usuario_nome.replace(/'/g, "\\'")}')">
                                <i class="bi bi-reply-fill"></i> Responder
                            </button>
                            <div class="respostas-comentario" id="respostas-do-comentario-${idComentario}" style="margin-top: 10px; padding-left: 14px; border-left: 2px solid #ebe5f2;"></div>
                        </div>
                    </div>
                `;
                containerComentarios.insertAdjacentHTML('beforeend', novoHtml);
            }

            // 4. Atualiza os contadores na tela
            const contadorTitulo = document.getElementById('contador-comentarios-titulo');
            if (contadorTitulo) {
                contadorTitulo.textContent = parseInt(contadorTitulo.textContent || '0') + 1;
            }
        }
    })
    .catch(error => console.error('Erro ao comentar:', error));
}

// Funções para gerenciar o modo de resposta no formulário
function prepararResposta(idPai, nomeAutor) {
    const inputPai = document.getElementById('input_id_pai');
    const indicador = document.getElementById('indicador-resposta');
    const autorSpan = document.getElementById('nome-autor-resposta');
    const campo = document.getElementById('campo_conteudo_comentario');

    if (inputPai) inputPai.value = idPai;
    if (autorSpan) autorSpan.innerText = nomeAutor;
    if (indicador) indicador.style.display = 'flex';
    
    if (campo) {
        campo.placeholder = `Escreva sua resposta para @${nomeAutor}...`;
        campo.focus();
        campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function cancelarResposta() {
    const inputPai = document.getElementById('input_id_pai');
    const indicador = document.getElementById('indicador-resposta');
    const campo = document.getElementById('campo_conteudo_comentario');

    if (inputPai) inputPai.value = '';
    if (indicador) indicador.style.display = 'none';
    if (campo) campo.placeholder = 'Escreva um comentário...';
}
// Função para abrir e fechar o menu de três pontinhos
function alternarMenuPost(event, menuId) {
    event.stopPropagation(); // Evita que o clique abra o link do post por acidente
    
    const menuAtual = document.getElementById(menuId);
    const todosOsMenus = document.querySelectorAll('.menu-opcoes-post');
    
    // Fecha qualquer outro menu de post que esteja aberto na tela
    todosOsMenus.forEach(menu => {
        if (menu.id !== menuId) {
            menu.style.display = 'none';
        }
    });

    // Alterna o estado do menu atual
    if (menuAtual.style.display === 'block') {
        menuAtual.style.display = 'none';
    } else {
        menuAtual.style.display = 'block';
    }
}

// Fecha o menu se o usuário clicar em qualquer outro lugar vazio da tela
window.addEventListener('click', function() {
    const todosOsMenus = document.querySelectorAll('.menu-opcoes-post');
    todosOsMenus.forEach(menu => {
        menu.style.display = 'none';
    });
});

// Função para abrir e fechar o menu de três pontinhos na tela de detalhes
function alternarMenuPost(event, menuId) {
    event.stopPropagation(); // Impede que o clique interfira em outros cliques da página
    
    const menuAtual = document.getElementById(menuId);
    
    if (menuAtual.style.display === 'block') {
        menuAtual.style.display = 'none';
    } else {
        menuAtual.style.display = 'block';
    }
}

// Fecha o menu de opções automaticamente se o usuário clicar em qualquer outro lugar fora
window.addEventListener('click', function() {
    const todosOsMenus = document.querySelectorAll('.menu-opcoes-post');
    todosOsMenus.forEach(menu => {
        menu.style.display = 'none';
    });
});

function alternarSalvar(botao) {
    const idPost = botao.getAttribute('data-id');
    const token = botao.getAttribute('data-token');

    fetch(`/publicacao/${idPost}/salvar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return;

        const icone = botao.querySelector('i');
        const contador = botao.querySelector('.contador-salvos');
        let total = parseInt(contador.innerText) || 0;

        if (data.status === 'salvo') {
            botao.classList.add('salvo');
            icone.className = 'bi bi-bookmark-fill';
            contador.innerText = total + 1;
        } else {
            botao.classList.remove('salvo');
            icone.className = 'bi bi-bookmark';
            contador.innerText = Math.max(0, total - 1);
        }
    })
    .catch(error => console.error('Erro ao atualizar salvamento:', error));
}


function abrirModalCompartilharData(elemento) {
    const id = elemento.dataset.id;
    const titulo = elemento.dataset.titulo;
    const url = elemento.dataset.url;

    // Chame a sua lógica existente passando as variáveis
    abrirModalCompartilhar(id, titulo, url);
}

