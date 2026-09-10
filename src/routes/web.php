<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacaoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ExplorarController;
use App\Http\Controllers\EsqueciSenhaController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\MetricasController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\CheckAdmin;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Visitantes / Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/', 'inicial')->name('inicial');

    // Cadastro de Usuários
    Route::get('/cadastro', [AutenticacaoController::class, 'exibirCadastro'])->name('cadastro');
    Route::post('/cadastro', [AutenticacaoController::class, 'registrar']);

    // Login do Sistema
    Route::get('/login', [AutenticacaoController::class, 'exibirLogin'])->name('login');
    Route::post('/login', [AutenticacaoController::class, 'logar']);

    // Autenticação via Google
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // Recuperação de Senha
    Route::get('/esqueci-senha', function () {
        return view('autenticacao.esqueci-senha');
    })->name('senha.esqueci');

    Route::post('/esqueci-senha', [EsqueciSenhaController::class, 'enviarRecuperacao'])->name('senha.enviar');
    Route::get('/redefinir-senha/{token}', [EsqueciSenhaController::class, 'exibirRedefinirSenha'])->name('senha.redefinir');
    Route::post('/redefinir-senha', [EsqueciSenhaController::class, 'redefinirSenha'])->name('senha.salvar');

    // Página Sobre
    Route::get('/sobre', function () {
        return view('sobre');
    })->name('sobre');
});

/*
|--------------------------------------------------------------------------
| Rotas Privadas (Usuários Autenticados / Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Feed e Explorar
    Route::get('/feed', [PublicacaoController::class, 'listarFeed'])->name('feed');
    Route::get('/explorar', [ExplorarController::class, 'index'])->name('explorar');

    // Autenticação / Sessão
    Route::post('/sair', [AutenticacaoController::class, 'sair'])->name('logout');

    /* --- MÓDULO DE PUBLICAÇÕES --- */
    Route::get('/publicacao/criar', [PublicacaoController::class, 'criar'])->name('publicacao.criar');
    Route::post('/publicacao/salvar', [PublicacaoController::class, 'salvar'])->name('publicacao.salvar');
    Route::get('/publicacao/{id}/detalhes', [PublicacaoController::class, 'detalhes'])->name('publicacao.detalhes');
    Route::get('/publicacao/{id}/editar', [PublicacaoController::class, 'editar'])->name('publicacao.editar');
    Route::put('/publicacao/{id}/atualizar', [PublicacaoController::class, 'atualizar'])->name('publicacao.atualizar');
    Route::delete('/publicacao/{id}', [PublicacaoController::class, 'deletar'])->name('publicacao.deletar');
    Route::post('/publicacao/{id}/curtir', [PublicacaoController::class, 'curtir'])->name('publicacao.curtir');
    Route::post('/publicacao/{id}/salvar', [PublicacaoController::class, 'alternarSalvar'])->name('publicacao.salvar.alternar');
    Route::post('/publicacao/{id}/compartilhar', [PublicacaoController::class, 'compartilhar'])->name('publicacao.compartilhar');

    /* --- MÓDULO DE PERFIL (USA NOME DE USUÁRIO) --- */
    Route::get('/perfil/{nome_usuario}', [PerfilController::class, 'exibir'])->name('perfil.exibir');
    Route::post('/perfil/atualizar/{id}', [PerfilController::class, 'atualizar'])->name('perfil.atualizar');
    Route::delete('/perfil/excluir', [PerfilController::class, 'excluirConta'])->name('perfil.excluir');

    /* --- MÓDULO DE REDE SOCIAL (SEGUIDORES) --- */
    Route::post('/perfil/{nome_usuario}/seguir', [PerfilController::class, 'seguir'])->name('perfil.seguir');
    Route::get('/perfil/{nome_usuario}/seguidores', [PerfilController::class, 'listarSeguidores'])->name('perfil.seguidores');
    Route::get('/perfil/{nome_usuario}/seguindo', [PerfilController::class, 'listarSeguindo'])->name('perfil.seguindo');

    /* --- CONFIGURAÇÕES E SENHA --- */
    Route::get('/configuracoes/senha', [PerfilController::class, 'telaAlterarSenha'])->name('senha.alterar');
    Route::put('/configuracoes/senha', [PerfilController::class, 'alterarSenha'])->name('senha.atualizar');

    /* --- COMENTÁRIOS --- */
    Route::post('/comentario/salvar', [ComentarioController::class, 'salvar'])->name('comentario.salvar');

    /* --- DASHBOARD DE MÉTRICAS --- */
    Route::get('/metricas', [MetricasController::class, 'index'])->name('metricas');

    /* --- NOTIFICAÇÕES --- */
    Route::get('/notificacoes', [NotificacaoController::class, 'listar'])->name('notificacoes.listar');
    Route::get('/notificacoes/ultimas', [NotificacaoController::class, 'ultimas'])->name('notificacoes.ultimas');
    Route::get('/notificacoes/{id}/ler', [NotificacaoController::class, 'marcarUmaComoLida'])->name('notificacoes.ler');
    Route::post('/notificacoes/marcar-lidas', [NotificacaoController::class, 'marcarLidas'])->name('notificacoes.marcarLidas');
    Route::post('/notificacoes/limpar', [NotificacaoController::class, 'limpar'])->name('notificacoes.limpar');
});

/*
|--------------------------------------------------------------------------
| Rotas de Administração (Apenas Administradores)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckAdmin::class])->prefix('admin')->group(function () {
    Route::delete('/usuarios/{nome_usuario}', [AdminController::class, 'deletarUsuario'])->name('admin.usuarios.deletar');
});