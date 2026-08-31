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

            // 2. Remove o aviso "Seja o primeiro a comentar" se ele existir
            const semComentarios = document.querySelector('.sem-comentarios');
            if (semComentarios) {
                semComentarios.remove();
            }

            // 3. Injeta o novo comentário na lista
            const containerComentarios = document.querySelector('.lista-comentarios');
            const novoHtml = `
                <div class="comentario" style="animation: fadeIn 0.4s ease;">
                    <a href="${data.usuario_perfil_url}">
                        <img src="${data.usuario_foto}" class="avatar-comentario">
                    </a>
                    <div class="corpo-comentario">
                        <div class="topo-comentario">
                            <a href="${data.usuario_perfil_url}" style="text-decoration: none; color: inherit;">
                                <strong>${data.usuario_nome}</strong>
                            </a>
                        </div>
                        <p>${data.conteudo}</p>
                    </div>
                </div>
            `;
            containerComentarios.insertAdjacentHTML('beforeend', novoHtml);

            // 4. Atualiza os contadores na tela
            const contadorTitulo = document.getElementById('contador-comentarios-titulo');
            if (contadorTitulo) {
                contadorTitulo.textContent = parseInt(contadorTitulo.textContent) + 1;
            }
        }
    })
    .catch(error => console.error('Erro ao comentar:', error));
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