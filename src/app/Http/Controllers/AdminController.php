<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function deletarUsuario(string $nome_usuario)
    {
        $usuario = Usuario::where('nome_usuario', $nome_usuario)->firstOrFail();

        // Evita que o Admin exclua a si próprio
        if (Auth::id() == $usuario->id_usuario) {
            return back()->with('erro', 'Você não pode excluir sua própria conta de administrador.');
        }

        $usuario->delete();

        return redirect()->route('feed')->with('sucesso', 'Usuário excluído com sucesso!');
    }
}