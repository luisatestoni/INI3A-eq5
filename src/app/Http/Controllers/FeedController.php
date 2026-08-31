<?php namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Models\Publicacao; 
class FeedController extends Controller { 
    public function index(Request $request)
    {
        $query = Publicacao::with(['usuario.perfil', 'curtidas', 'comentarios']);

        // Verifica se o usuário digitou algo na busca
        if ($request->has('busca') && !empty($request->busca)) {
            $termo = $request->busca;
            
            $query->where(function($q) use ($termo) {
                $q->where('titulo', 'LIKE', "%{$termo}%")
                ->orWhere('conteudo', 'LIKE', "%{$termo}%");
            });
        }

        $posts = $query->latest()->get();

        return view('feed', compact('posts'));
    }
}