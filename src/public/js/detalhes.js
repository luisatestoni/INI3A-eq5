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
    event.preventDefault(); // Impede o navegador de recarregar a página!

    const url = formulario.action;
    const formData = new FormData(formulario);

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Avisa o Laravel que é um envio AJAX
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            // 1. Limpa o campo de texto digitado
            formulario.querySelector('textarea').value = '';

            // 2. Injeta o novo comentário no HTML dinamicamente
            const containerComentarios = document.querySelector('.lista-comentarios'); // ajuste para a sua classe de bloco
            
            const novoHtml = `
                <div class="comentario" style="animation: fadeIn 0.4s ease;">
                    <img src="${data.usuario_foto}" class="avatar-comentario">
                    <div class="corpo-comentario">
                        <div class="topo-comentario">
                            <strong>${data.usuario_nome}</strong>
                        </div>
                        <p>${data.conteudo}</p>
                    </div>
                </div>
            `;
            
            // Coloca o novo comentário no topo ou no final da lista
            containerComentarios.insertAdjacentHTML('beforeend', novoHtml);
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