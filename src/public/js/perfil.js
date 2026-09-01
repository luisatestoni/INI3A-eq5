document.addEventListener('DOMContentLoaded', function() {
    // ---- MODAL DE EDITAR PERFIL ----
    const modal = document.getElementById('modal-editar-perfil');
    const btnAbrir = document.getElementById('btn-abrir-editar');
    const btnFechar = document.getElementById('btn-fechar-editar');

    if (btnAbrir && modal) {
        btnAbrir.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
        });
    }

    if (btnFechar && modal) {
        btnFechar.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    if (modal) {
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // ---- POPUP DE CONFIGURAÇÕES (ENGRENAGEM) ----
    const popupConfig = document.getElementById('popup-config');
    const abrirConfig = document.getElementById('abrir-config');
    const fecharConfig = document.getElementById('fechar-config');

    if (abrirConfig && popupConfig) {
        abrirConfig.addEventListener('click', (e) => {
            e.stopPropagation();
            popupConfig.classList.toggle('ativo');
        });
    }

    if (fecharConfig && popupConfig) {
        fecharConfig.addEventListener('click', () => {
            popupConfig.classList.remove('ativo');
        });
    }

    document.addEventListener('click', function(e) {
        if (
            popupConfig &&
            !popupConfig.contains(e.target) &&
            (!abrirConfig || !abrirConfig.contains(e.target))
        ) {
            popupConfig.classList.remove('ativo');
        }
    });

    // ---- EXCLUSÃO DA CONTA (Adicionado aqui com segurança) ----
    const btnExcluirConta = document.getElementById('btn-excluir-conta');
    
    if (btnExcluirConta) {
        btnExcluirConta.addEventListener('click', function() {
            const confirmacao1 = confirm("ATENÇÃO: Tem certeza absoluta de que deseja excluir sua conta? Isso apagará todas as suas publicações permanentemente.");
            
            if (confirmacao1) {
                const confirmacao2 = confirm("Esta ação não pode ser desfeita! Deseja mesmo prosseguir?");
                
                if (confirmacao2) {
                    // Descobre dinamicamente o Token CSRF do site usando o formulário de logout que já está na tela
                    const csrfToken = document.querySelector('input[name="_token"]')?.value;

                    if (!csrfToken) {
                        alert("Erro de segurança: Token CSRF não encontrado. Recarregue a página.");
                        return;
                    }

                    // Cria um formulário invisível para disparar o método DELETE do Laravel
                    const form = document.createElement('form');
                    form.action = "/perfil/excluir";
                    form.method = "POST";

                    // Input simulando o método DELETE requerido pela sua rota
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    // Input com o token de segurança obrigatório
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    form.appendChild(methodInput);
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    
                    form.submit(); // Envia os dados para o servidor processar a exclusão
                }
            }
        });
    }
});

// ---- FUNÇÕES GLOBAIS (PREVIEWS E SEGUIR) ----

// Preview do Avatar
function previewImagem(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview da Capa
function previewCapa(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('capa-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Sistema Assíncrono de Seguir
function alternarSeguir(botao) {
    const idUsuarioSeguido = botao.getAttribute('data-id');
    const tokenCsrf = botao.getAttribute('data-token');
    const contador = document.getElementById('contador-seguidores');

    fetch(`/perfil/${idUsuarioSeguido}/seguir`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenCsrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'sucesso') {
            let numeroAtual = parseInt(contador.textContent);
            
            if (data.seguindo) {
                botao.classList.add('seguindo');
                botao.textContent = 'Seguindo';
                contador.textContent = numeroAtual + 1;
            } else {
                botao.classList.remove('seguindo');
                botao.textContent = 'Seguir';
                contador.textContent = numeroAtual - 1;
            }
        }
    })
    .catch(error => console.error('Erro ao processar ação:', error));
}

// ---- MODAL SEGUIDORES E SEGUINDO (ESTILO INSTAGRAM) ----
    const modalConexoes = document.getElementById('modal-conexoes');
    const tituloModal = document.getElementById('modal-conexoes-titulo');
    const listaModal = document.getElementById('modal-conexoes-lista');
    const btnFecharConexoes = document.getElementById('fechar-modal-conexoes');
    
    const btnSeguidores = document.getElementById('abrir-modal-seguidores');
    const btnSeguindo = document.getElementById('abrir-modal-seguindo');

    function carregarListaConexoes(tipo, idUsuario) {
        tituloModal.textContent = tipo === 'seguidores' ? 'Seguidores' : 'Seguindo';
        listaModal.innerHTML = '<p class="modal-conexoes-carregando">Carregando...</p>';
        modalConexoes.style.display = 'flex';

        fetch(`/perfil/${idUsuario}/${tipo}`)
            .then(response => response.json())
            .then(usuarios => {
                listaModal.innerHTML = '';

                if (usuarios.length === 0) {
                    listaModal.innerHTML = '<p class="modal-conexoes-vazio">Nenhum usuário encontrado.</p>';
                    return;
                }

                usuarios.forEach(user => {
                    const fotoUrl = user.perfil && user.perfil.foto ? `/storage/${user.perfil.foto}` : '/imagens/perfil-v1.png';
                    
                    const itemUsuario = document.createElement('div');
                    itemUsuario.className = 'modal-item-usuario';

                    itemUsuario.innerHTML = `
                        <a href="/perfil/${user.id_usuario}" class="avatar-link">
                            <img src="${fotoUrl}" alt="${user.nome_usuario}">
                        </a>
                        <a href="/perfil/${user.id_usuario}" class="info-link">
                            ${user.nome_usuario}
                        </a>
                    `;
                    listaModal.appendChild(itemUsuario);
                });
            })
            .catch(error => {
                console.error('Erro ao buscar conexões:', error);
                listaModal.innerHTML = '<p class="modal-conexoes-vazio" style="color:red;">Erro ao carregar lista.</p>';
            });
    }

    if (btnSeguidores) {
        btnSeguidores.addEventListener('click', function(e) {
            e.preventDefault();
            carregarListaConexoes('seguidores', this.getAttribute('data-id'));
        });
    }

    if (btnSeguindo) {
        btnSeguindo.addEventListener('click', function(e) {
            e.preventDefault();
            carregarListaConexoes('seguindo', this.getAttribute('data-id'));
        });
    }

    if (btnFecharConexoes) {
        btnFecharConexoes.addEventListener('click', () => {
            modalConexoes.style.display = 'none';
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target === modalConexoes) {
            modalConexoes.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
    // 1. Controle das Abas Principais (Atividade, Capítulos, Salvos e curtidos)
    const abasPrincipais = document.querySelectorAll('.abas-perfil .aba');
    const conteudosAba = document.querySelectorAll('.conteudo-aba');

    abasPrincipais.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');

            // Reseta botões principais e esconde todas as abas
            abasPrincipais.forEach(b => b.classList.remove('ativa'));
            conteudosAba.forEach(c => c.style.display = 'none');

            // Ativa o botão clicado e mostra o conteúdo correspondente
            btn.classList.add('ativa');
            const elConteudo = document.getElementById(`aba-${targetTab}`);
            if (elConteudo) {
                elConteudo.style.display = 'block';
            }
        });
    });

    // 2. Controle dos Botões Internos (Salvos vs Curtidos)
    const botoesFiltro = document.querySelectorAll('.btn-filtro-aba');
    const paineisSubAba = document.querySelectorAll('.painel-conteudo-aba');

    botoesFiltro.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetSubTab = btn.getAttribute('data-subtab');

            botoesFiltro.forEach(b => b.classList.remove('ativo'));
            paineisSubAba.forEach(p => p.style.display = 'none');

            btn.classList.add('ativo');
            const elSubPainel = document.getElementById(targetSubTab);
            if (elSubPainel) {
                elSubPainel.style.display = 'block';
            }
        });
    });
});

