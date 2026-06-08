<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PublicacaoController;

Route::middleware('guest')->group(function () {
    Route::view('/', 'inicial')->name('inicial');

    Route::get('/cadastro', [AutenticacaoController::class, 'exibirCadastro'])
        ->name('cadastro');

    Route::post('/cadastro', [AutenticacaoController::class, 'registrar']);

    Route::get('/login', [AutenticacaoController::class, 'exibirLogin'])
        ->name('login');

    Route::post('/login', [AutenticacaoController::class, 'logar']);

    Route::get('/esqueci-senha', function () {
        return view('autenticacao.esqueci-senha');
    })->name('senha.esqueci');

    Route::post('/esqueci-senha', [AutenticacaoController::class, 'enviarRecuperacao'])
        ->name('senha.enviar');
});

Route::middleware('auth')->group(function () {
    Route::post('/sair', [AutenticacaoController::class, 'sair'])
        ->name('sair');

    Route::get('/feed', [PublicacaoController::class, 'listarFeed'])
        ->name('feed');

    Route::get('/publicacao/criar', [PublicacaoController::class, 'criar'])
        ->name('publicacao.criar');

    Route::post('/publicacao/salvar', [PublicacaoController::class, 'salvar'])
        ->name('publicacao.salvar');

    Route::get('/publicacao/{id}/detalhes', [PublicacaoController::class, 'detalhes'])
        ->name('publicacao.detalhes');

    Route::post('/perfil/atualizar', [PerfilController::class, 'atualizar'])
        ->name('perfil.atualizar');

    Route::delete('/perfil/excluir', [PerfilController::class, 'excluirConta'])
        ->name('perfil.excluir');

    Route::get('/perfil/alterar-senha', [PerfilController::class, 'telaAlterarSenha'])
        ->name('perfil.alterarSenha.form');

    Route::post('/perfil/alterar-senha', [PerfilController::class, 'alterarSenha'])
        ->name('perfil.alterarSenha');

    Route::get('/perfil/{id}/seguidores', [PerfilController::class, 'listarSeguidores'])
        ->name('perfil.seguidores');

    Route::get('/perfil/{id}/seguindo', [PerfilController::class, 'listarSeguindo'])
        ->name('perfil.seguindo');

    Route::get('/perfil/{id}', [PerfilController::class, 'exibir'])
        ->name('perfil.exibir');
});