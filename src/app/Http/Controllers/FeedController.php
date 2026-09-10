<?php 

namespace App\Http\Controllers; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\Publicacao; 
use App\Models\Usuario;

class FeedController extends Controller 
{ 
    public function index(Request $request)
    {
        $aba = $request->query('aba', 'para-voce');
        $categoriaSelecionada = $request->query('categoria');

        $query = Publicacao::with(['usuario.perfil', 'curtidas', 'comentarios', 'salvos']);

        // 1. Filtro de Busca por texto
        if ($request->filled('busca')) {
            $termo = $request->busca;
            $query->where(function($q) use ($termo) {
                $q->where('titulo', 'LIKE', "%{$termo}%")
                  ->orWhere('conteudo', 'LIKE', "%{$termo}%");
            });
        }

        // 2. Filtro da Aba Selecionada
        if ($aba === 'seguindo') {
            /** @var Usuario|null $usuario */
            $usuario = Auth::user();

            if ($usuario) {
                // Obtém os IDs dos usuários que o usuário logado segue
                $idsSeguidos = $usuario->seguindo()->pluck('usuarios.id_usuario'); 
                $query->whereIn('fk_id_usuario', $idsSeguidos);
            } else {
                // Caso não haja usuário autenticado, retorna lista vazia
                $query->whereRaw('1 = 0');
            }

            $query->latest('data_publicacao');

        } elseif ($aba === 'tendencias') {
            $query->withCount('curtidas')
                  ->orderBy('curtidas_count', 'desc');

        } else {
            // Aba 'para-voce' (Padrão)
            $query->latest('data_publicacao');
        }

        // 3. Filtro de Categoria
        if ($categoriaSelecionada) {
            $query->where('categorias', 'LIKE', '%' . $categoriaSelecionada . '%');
        }

        $publicacoes = $query->get();

        return view('feed', compact('publicacoes', 'aba', 'categoriaSelecionada'));
    }
}