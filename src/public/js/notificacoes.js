/**
 * SCRIBO - GERENCIADOR DE NOTIFICAÇÕES (DROPDOWN & TEMPO REAL)
 */
document.addEventListener('DOMContentLoaded', function() {
    const btnSino = document.getElementById('btn-notificacoes-header');
    const dropdownNotif = document.getElementById('dropdown-notificacoes-header');
    const badgeSino = document.getElementById('badge-notificacoes-count');
    const listaNotif = document.getElementById('lista-dropdown-notificacoes');
    const btnMarcarLidas = document.getElementById('btn-marcar-todas-lidas-header');

    if (btnSino && dropdownNotif) {
        btnSino.addEventListener('click', function(e) {
            e.stopPropagation();
            const estaAberto = dropdownNotif.classList.contains('ativo');

            // Fecha outros popups
            fecharTodosDropdowns();

            if (!estaAberto) {
                dropdownNotif.classList.add('ativo');
                carregarNotificacoesRecentes();
            }
        });
    }

    // Fechar ao clicar fora
    document.addEventListener('click', function(e) {
        if (dropdownNotif && !dropdownNotif.contains(e.target) && (!btnSino || !btnSino.contains(e.target))) {
            dropdownNotif.classList.remove('ativo');
        }
    });

    if (btnMarcarLidas) {
        btnMarcarLidas.addEventListener('click', function(e) {
            e.preventDefault();
            const tokenCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                           || document.querySelector('input[name="_token"]')?.value;

            fetch('/notificacoes/marcar-lidas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenCsrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    // Limpa badge
                    if (badgeSino) {
                        badgeSino.textContent = '0';
                        badgeSino.style.display = 'none';
                    }

                    // Remove estilo de não lidas da lista atual
                    const itens = document.querySelectorAll('.item-drop-notif.nao-lida');
                    itens.forEach(el => el.classList.remove('nao-lida'));

                    if (typeof mostrarToast === 'function') {
                        mostrarToast('Todas as notificações foram marcadas como lidas.', 'sucesso');
                    }
                }
            })
            .catch(err => console.error('Erro ao marcar notificações:', err));
        });
    }

    function carregarNotificacoesRecentes() {
        if (!listaNotif) return;

        listaNotif.innerHTML = '<div class="carregando-notif"><i class="bi bi-arrow-clockwise girando"></i> Carregando...</div>';

        fetch('/notificacoes/ultimas', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            // Atualiza badge
            if (badgeSino) {
                if (data.total_nao_lidas > 0) {
                    badgeSino.textContent = data.total_nao_lidas > 99 ? '99+' : data.total_nao_lidas;
                    badgeSino.style.display = 'flex';
                } else {
                    badgeSino.style.display = 'none';
                }
            }

            if (!data.notificacoes || data.notificacoes.length === 0) {
                listaNotif.innerHTML = `
                    <div class="vazio-dropdown-notif">
                        <i class="bi bi-bell-slash"></i>
                        <span>Nenhuma notificação recente</span>
                    </div>
                `;
                return;
            }

            let html = '';
            data.notificacoes.forEach(n => {
                const classeLida = n.lida ? '' : 'nao-lida';
                const icone = obterIconeTipo(n.tipo);

                html += `
                    <a href="${n.url}" class="item-drop-notif ${classeLida}">
                        <div class="avatar-drop-wrapper">
                            <img src="${n.autor_foto}" alt="${n.autor_nome}" class="avatar-drop-img">
                            <span class="icone-tipo-drop ${n.tipo}">
                                <i class="bi ${icone}"></i>
                            </span>
                        </div>
                        <div class="info-drop-notif">
                            <p class="texto-drop-notif">
                                <strong>${n.autor_nome}</strong> ${n.mensagem}
                            </p>
                            <span class="tempo-drop-notif">${n.tempo}</span>
                        </div>
                        ${!n.lida ? '<span class="ponto-nao-lida"></span>' : ''}
                    </a>
                `;
            });

            listaNotif.innerHTML = html;
        })
        .catch(err => {
            console.error('Erro ao carregar notificações:', err);
            listaNotif.innerHTML = '<div class="erro-drop-notif">Não foi possível carregar as notificações.</div>';
        });
    }

    function obterIconeTipo(tipo) {
        switch (tipo) {
            case 'curtida': return 'bi-heart-fill';
            case 'comentario': return 'bi-chat-fill';
            case 'seguidor': return 'bi-person-plus-fill';
            case 'compartilhamento': return 'bi-share-fill';
            default: return 'bi-bell-fill';
        }
    }

    function fecharTodosDropdowns() {
        const dropMenuUsuario = document.getElementById('dropdown-menu-usuario');
        if (dropMenuUsuario) dropMenuUsuario.classList.remove('ativo');
    }
});
