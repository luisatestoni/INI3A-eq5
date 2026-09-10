/**
 * SCRIBO - GERENCIADOR DE COMPARTILHAMENTO UNIVERSAL
 */
let postIdAtualCompartilhar = null;
let postUrlAtualCompartilhar = '';
let postTituloAtualCompartilhar = '';

function abrirModalCompartilhar(id, titulo, urlOpcional) {
    postIdAtualCompartilhar = id;
    postTituloAtualCompartilhar = titulo || 'Confira esta história no Scribo';
    
    // Se a URL não for passada, monta a URL absoluta da publicação
    const urlFinal = urlOpcional || `${window.location.origin}/publicacao/${id}/detalhes`;
    postUrlAtualCompartilhar = urlFinal;

    // Atualiza campos visuais da modal
    const elTitulo = document.getElementById('comp-titulo-post');
    const elInput = document.getElementById('input-link-compartilhar');
    const modal = document.getElementById('modal-compartilhar-overlay');
    const btnCopiar = document.getElementById('btn-copiar-link');

    if (elTitulo) elTitulo.textContent = postTituloAtualCompartilhar;
    if (elInput) elInput.value = postUrlAtualCompartilhar;

    // Restaura botão de copiar
    if (btnCopiar) {
        btnCopiar.innerHTML = '<i class="bi bi-link-45deg"></i> <span>Copiar</span>';
        btnCopiar.classList.remove('copiado');
    }

    // Configura links das redes sociais
    const textoCodificado = encodeURIComponent(postTituloAtualCompartilhar);
    const urlCodificada = encodeURIComponent(postUrlAtualCompartilhar);

    const linkWhatsapp = document.getElementById('share-whatsapp');
    if (linkWhatsapp) {
        linkWhatsapp.href = `https://api.whatsapp.com/send?text=${textoCodificado}%20${urlCodificada}`;
    }

    const linkTwitter = document.getElementById('share-twitter');
    if (linkTwitter) {
        linkTwitter.href = `https://twitter.com/intent/tweet?text=${textoCodificado}&url=${urlCodificada}`;
    }

    const linkTelegram = document.getElementById('share-telegram');
    if (linkTelegram) {
        linkTelegram.href = `https://t.me/share/url?url=${urlCodificada}&text=${textoCodificado}`;
    }

    const linkFacebook = document.getElementById('share-facebook');
    if (linkFacebook) {
        linkFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${urlCodificada}`;
    }

    const linkLinkedin = document.getElementById('share-linkedin');
    if (linkLinkedin) {
        linkLinkedin.href = `https://www.linkedin.com/sharing/share-offsite/?url=${urlCodificada}`;
    }

    // Web Share API nativa (smartphones/tablets)
    const btnNativo = document.getElementById('share-nativo');
    if (btnNativo) {
        if (navigator.share) {
            btnNativo.style.display = 'flex';
        } else {
            btnNativo.style.display = 'none';
        }
    }

    // Abre a modal
    if (modal) {
        modal.style.display = 'flex';
    }
}

function fecharModalCompartilhar() {
    const modal = document.getElementById('modal-compartilhar-overlay');
    if (modal) {
        modal.style.display = 'none';
    }
}

function copiarLinkCompartilhar() {
    const input = document.getElementById('input-link-compartilhar');
    if (!input) return;

    input.select();
    input.setSelectionRange(0, 99999);

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value).then(() => {
            exibirSucessoCopia();
        }).catch(() => {
            document.execCommand('copy');
            exibirSucessoCopia();
        });
    } else {
        document.execCommand('copy');
        exibirSucessoCopia();
    }

    // Registra compartilhamento no backend
    registrarCompartilhamento();
}

function exibirSucessoCopia() {
    const btnCopiar = document.getElementById('btn-copiar-link');
    if (btnCopiar) {
        btnCopiar.innerHTML = '<i class="bi bi-check2"></i> <span>Copiado!</span>';
        btnCopiar.classList.add('copiado');
    }

    mostrarToast('Link copiado para a área de transferência!', 'sucesso');
}

function compartilharNativo() {
    if (navigator.share) {
        navigator.share({
            title: postTituloAtualCompartilhar,
            text: postTituloAtualCompartilhar,
            url: postUrlAtualCompartilhar
        }).then(() => {
            registrarCompartilhamento();
            fecharModalCompartilhar();
        }).catch(err => {
            console.log('Compartilhamento cancelado:', err);
        });
    }
}

function registrarCompartilhamento() {
    if (!postIdAtualCompartilhar) return;

    const tokenCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                   || document.querySelector('input[name="_token"]')?.value;

    fetch(`/publicacao/${postIdAtualCompartilhar}/compartilhar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': tokenCsrf,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            // Atualiza contadores na tela caso existam
            const contadores = document.querySelectorAll(`[data-compartilhar-id="${postIdAtualCompartilhar}"]`);
            contadores.forEach(el => {
                const spanNum = el.querySelector('.contador-compartilhamentos') || el;
                spanNum.textContent = data.total_compartilhamentos;
            });
        }
    })
    .catch(err => console.error('Erro ao registrar compartilhamento:', err));
}

// Fecha ao clicar fora da modal
window.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-compartilhar-overlay');
    if (e.target === modal) {
        fecharModalCompartilhar();
    }
});

// Toast Helper Universal
function mostrarToast(mensagem, tipo = 'info') {
    let container = document.getElementById('container-toasts-global');
    if (!container) {
        container = document.createElement('div');
        container.id = 'container-toasts-global';
        container.className = 'container-toasts-global';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-notificacao toast-${tipo}`;
    
    const icone = tipo === 'sucesso' ? 'bi-check-circle-fill' : 'bi-info-circle-fill';
    toast.innerHTML = `<i class="bi ${icone}"></i> <span>${mensagem}</span>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('saindo');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}
