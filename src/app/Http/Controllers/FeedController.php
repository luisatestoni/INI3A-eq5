<?php namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Models\Publicacao; 
class FeedController extends Controller { 
    public function index()
    {
        $publicacoes = Publicacao::with(['usuario.perfil'])
            ->withCount('curtidas')
            ->orderBy('data_publicacao', 'desc')
            ->get();

        return view('feed', compact('publicacoes'));
    }
}