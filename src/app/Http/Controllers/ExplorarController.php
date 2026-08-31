<?php

namespace App\Http\Controllers;

use App\Models\Publicacao; // Ajuste se no seu projeto for Post
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ExplorarController extends BaseController
{
    public function index(Request $request)
    {
        $busca = trim($request->input('busca', ''));
        $tipo = $request->input('tipo', 'tudo'); // 'tudo', 'publicacoes', 'usuarios'

        $usuarios = collect();
        $publicacoes = collect();

        if (!empty($busca)) {
            // 1. Busca por Usuários (Nome ou @nome_usuario)
            if ($tipo === 'tudo' || $tipo === 'usuarios') {
                $usuarios = Usuario::with(['perfil', 'seguidores'])
                    ->where('nome', 'LIKE', "%{$busca}%")
                    ->orWhere('nome_usuario', 'LIKE', "%{$busca}%")
                    ->take(12)
                    ->get();
            }

            // 2. Busca por Publicações (Título, Resumo, Conteúdo ou Categorias)
            if ($tipo === 'tudo' || $tipo === 'publicacoes') {
                $publicacoes = Publicacao::with(['usuario.perfil', 'curtidas', 'comentarios'])
                    ->where('titulo', 'LIKE', "%{$busca}%")
                    ->orWhere('resumo', 'LIKE', "%{$busca}%")
                    ->orWhere('conteudo', 'LIKE', "%{$busca}%")
                    ->orWhere('categorias', 'LIKE', "%{$busca}%")
                    ->orderBy('data_publicacao', 'desc')
                    ->take(20)
                    ->get();
            }
        }

        return view('explorar', compact('busca', 'tipo', 'usuarios', 'publicacoes'));
    }
}