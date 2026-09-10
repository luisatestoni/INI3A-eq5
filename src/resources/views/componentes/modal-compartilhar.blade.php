<!-- MODAL UNIVERSAL DE COMPARTILHAMENTO -->
<div id="modal-compartilhar-overlay" class="modal-compartilhar-overlay" style="display: none;">
    <div class="modal-compartilhar-container">
        <div class="modal-compartilhar-cabecalho">
            <div class="titulo-modal-comp">
                <i class="bi bi-share-fill" style="color: #ff7a45;"></i>
                <h3>Compartilhar Publicação</h3>
            </div>
            <button type="button" class="btn-fechar-comp" onclick="fecharModalCompartilhar()">&times;</button>
        </div>

        <div class="modal-compartilhar-corpo">
            <!-- Prévia do post -->
            <div class="previa-post-compartilhar">
                <p class="titulo-post-comp" id="comp-titulo-post">Título da publicação</p>
                <span class="url-post-comp" id="comp-subtitulo-post">Scribo - Histórias que conectam</span>
            </div>

            <!-- Copiar Link -->
            <div class="bloco-copiar-link">
                <label for="input-link-compartilhar">Link direto:</label>
                <div class="input-copiar-wrapper">
                    <input type="text" id="input-link-compartilhar" readonly>
                    <button type="button" id="btn-copiar-link" class="btn-copiar-acao" onclick="copiarLinkCompartilhar()">
                        <i class="bi bi-link-45deg"></i>
                        <span>Copiar</span>
                    </button>
                </div>
            </div>

            <!-- Compartilhar em Redes Sociais -->
            <div class="bloco-redes-sociais">
                <span>Compartilhar via:</span>
                <div class="botoes-redes-grid">
                    <!-- WhatsApp -->
                    <a href="#" id="share-whatsapp" target="_blank" class="btn-rede btn-whatsapp" onclick="registrarCompartilhamento()">
                        <i class="bi bi-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>

                    <!-- X / Twitter -->
                    <a href="#" id="share-twitter" target="_blank" class="btn-rede btn-twitter" onclick="registrarCompartilhamento()">
                        <i class="bi bi-twitter-x"></i>
                        <span>X (Twitter)</span>
                    </a>

                    <!-- Telegram -->
                    <a href="#" id="share-telegram" target="_blank" class="btn-rede btn-telegram" onclick="registrarCompartilhamento()">
                        <i class="bi bi-telegram"></i>
                        <span>Telegram</span>
                    </a>

                    <!-- Facebook -->
                    <a href="#" id="share-facebook" target="_blank" class="btn-rede btn-facebook" onclick="registrarCompartilhamento()">
                        <i class="bi bi-facebook"></i>
                        <span>Facebook</span>
                    </a>

                    <!-- LinkedIn -->
                    <a href="#" id="share-linkedin" target="_blank" class="btn-rede btn-linkedin" onclick="registrarCompartilhamento()">
                        <i class="bi bi-linkedin"></i>
                        <span>LinkedIn</span>
                    </a>

                    <!-- Botão Nativo para Dispositivos Móveis -->
                    <button type="button" id="share-nativo" class="btn-rede btn-nativo" style="display: none;" onclick="compartilharNativo()">
                        <i class="bi bi-send-fill"></i>
                        <span>Mais opções</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
