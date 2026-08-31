<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacaoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ExplorarController;


/*
|--------------------------------------------------------------------------
| Rotas Públicas (Visitantes / Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Tela Inicial de boas-vindas do Scribo
    Route::view('/', 'inicial')->name('inicial');

    // Cadastro de Usuários
    Route::get('/cadastro', [AutenticacaoController::class, 'exibirCadastro'])->name('cadastro');
    Route::post('/cadastro', [AutenticacaoController::class, 'registrar']);

    // Login do Sistema
    Route::get('/login', [AutenticacaoController::class, 'exibirLogin'])->name('login');
    Route::post('/login', [AutenticacaoController::class, 'logar']);

    // Recuperação de Senha
    Route::get('/esqueci-senha', function () {
        return view('autenticacao.esqueci-senha');
    })->name('senha.esqueci');
    Route::post('/esqueci-senha', [AutenticacaoController::class, 'enviarRecuperacao'])->name('senha.enviar');
});

/*
|--------------------------------------------------------------------------
| Rotas Privadas (Usuários Autenticados / Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // LOGOUT (Ajustado o name para 'logout' para bater com a view do perfil)
    Route::post('/sair', [AutenticacaoController::class, 'sair'])->name('logout');

    // Feed Principal (Listagem de publicações)
    Route::get('/feed', [PublicacaoController::class, 'listarFeed'])->name('feed');

    /* --- MÓDULO DE PUBLICAÇÕES --- */
    Route::get('/publicacao/criar', [PublicacaoController::class, 'criar'])->name('publicacao.criar');
    Route::post('/publicacao/salvar', [PublicacaoController::class, 'salvar'])->name('publicacao.salvar');
    
    Route::get('/publicacao/{id}/detalhes', [PublicacaoController::class, 'detalhes'])->name('publicacao.detalhes');
    Route::get('/publicacao/{id}/editar', [PublicacaoController::class, 'editar'])->name('publicacao.editar');
    Route::put('/publicacao/{id}/atualizar', [PublicacaoController::class, 'atualizar'])->name('publicacao.atualizar');
    Route::delete('/publicacao/{id}', [PublicacaoController::class, 'deletar'])->name('publicacao.deletar');

    /* --- MÓDULO DE PERFIL --- */
    Route::get('/perfil/{id}', [PerfilController::class, 'exibir'])->name('perfil.exibir');
    Route::post('/perfil/atualizar', [PerfilController::class, 'atualizar'])->name('perfil.atualizar');
    Route::delete('/perfil/excluir', [PerfilController::class, 'excluirConta'])->name('perfil.excluir');

    /* --- ALTERAR SENHA --- */
    // Ajustado para bater com os nomes de rotas que criamos na view 'alterar-senha.blade.php'
    /* --- ALTERAR SENHA (CORRIGIDO) --- */
    // Mudamos de AutenticacaoController para PerfilController para bater com seus métodos existentes
    Route::get('/configuracoes/senha', [PerfilController::class, 'telaAlterarSenha'])
        ->name('senha.alterar');
        
    Route::put('/configuracoes/senha', [PerfilController::class, 'alterarSenha'])
        ->name('senha.atualizar');

    /* --- MÓDULO DE REDE SOCIAL (SEGUIDORES) --- */
    Route::post('/perfil/{id_usuario}/seguir', [PerfilController::class, 'seguir'])->name('perfil.seguir');
    Route::get('/perfil/{id}/seguidores', [PerfilController::class, 'listarSeguidores'])->name('perfil.seguidores');
    Route::get('/perfil/{id}/seguindo', [PerfilController::class, 'listarSeguindo'])->name('perfil.seguindo');

    /* --- COMENTÁRIOS --- */
    Route::post('/comentario/salvar', [ComentarioController::class, 'salvar'])->name('comentario.salvar');
    // Adicione esta engrenagem no bloco 'auth' das suas rotas privadas
    Route::post('/publicacao/{id}/curtir', [PublicacaoController::class, 'curtir'])->name('publicacao.curtir');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/explorar', [ExplorarController::class, 'index'])->name('explorar');
});

