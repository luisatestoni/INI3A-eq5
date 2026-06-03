<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome')->name('inicial');
    Route::get('/cadastro', [AutenticacaoController::class, 'exibirCadastro'])->name('cadastro');
    Route::post('/cadastro', [AutenticacaoController::class, 'cadastrar']);
    Route::get('/login', [AutenticacaoController::class, 'exibirLogin'])->name('login');
    Route::post('/login', [AutenticacaoController::class, 'logar']);
    Route::view('/esqueci-a-senha', 'auth.forgot-password')->name('senha.recuperar');
});

// --- TELAS PROTEGIDAS (LOGADO) ---
Route::middleware('auth')->group(function () {
    Route::post('/sair', [AutenticacaoController::class, 'sair'])->name('sair');

    Route::get('/feed', [PostController::class, 'listarFeed'])->name('feed');
    
    Route::get('/publicacao/criar', [PostController::class, 'criarPublicacao'])->name('post.criar');
    Route::post('/publicacao/salvar', [PostController::class, 'salvarPublicacao'])->name('post.salvar');
    Route::get('/publicacao/{id}', [PostController::class, 'exibirPost'])->name('post.exibir');

    Route::get('/perfil/{usuario}', [PerfilController::class, 'exibirPerfil'])->name('perfil.exibir');
    Route::post('/perfil/alterar-senha', [PerfilController::class, 'alterarSenha'])->name('perfil.alterar_senha');
});
