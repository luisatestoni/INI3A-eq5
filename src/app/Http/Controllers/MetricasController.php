<?php

namespace App\Http\Controllers;

use App\Models\Publicacao;
use App\Models\Usuario;
use App\Models\Comentario;
use App\Models\Curtida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MetricasController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        // 1. Todas as publicações do autor com contadores
        $minhasPublicacoes = Publicacao::where('fk_id_usuario', $usuario->id_usuario)
            ->withCount(['curtidas', 'comentarios', 'salvos'])
            ->orderBy('data_publicacao', 'desc')
            ->get();

        // 2. Indicadores Principais (KPIs)
        $totalPosts = $minhasPublicacoes->count();
        $totalCurtidas = $minhasPublicacoes->sum('curtidas_count');
        $totalComentarios = $minhasPublicacoes->sum('comentarios_count');
        $totalSalvos = $minhasPublicacoes->sum('salvos_count');
        $totalCompartilhamentos = $minhasPublicacoes->sum('compartilhamentos');
        $totalSeguidores = $usuario->seguidores()->count();
        $totalSeguindo = $usuario->seguindo()->count();

        // Total de engajamento acumulado
        $totalEngajamento = $totalCurtidas + $totalComentarios + $totalSalvos + $totalCompartilhamentos;
        $mediaEngajamentoPorPost = $totalPosts > 0 ? round($totalEngajamento / $totalPosts, 1) : 0;

        // 3. Top 5 Histórias com Maior Engajamento
        $topPublicacoes = $minhasPublicacoes->sortByDesc(function ($post) {
            return ($post->curtidas_count * 2) 
                 + ($post->comentarios_count * 3) 
                 + ($post->salvos_count * 2) 
                 + (($post->compartilhamentos ?? 0) * 4);
        })->take(5);

        // 4. Distribuição de Categorias publicadas pelo autor
        $distribuicaoCategorias = [];
        foreach ($minhasPublicacoes as $post) {
            if (!empty($post->categorias)) {
                $cats = explode(',', $post->categorias);
                foreach ($cats as $cat) {
                    $catLimpa = trim($cat);
                    if (!empty($catLimpa)) {
                        $distribuicaoCategorias[$catLimpa] = ($distribuicaoCategorias[$catLimpa] ?? 0) + 1;
                    }
                }
            }
        }
        arsort($distribuicaoCategorias);

        // Converte para percentual
        $totalMencoes = array_sum($distribuicaoCategorias);
        $categoriasPercentual = [];
        foreach ($distribuicaoCategorias as $nome => $qtd) {
            $categoriasPercentual[$nome] = [
                'qtd' => $qtd,
                'porcentagem' => $totalMencoes > 0 ? round(($qtd / $totalMencoes) * 100) : 0
            ];
        }

        // 5. Linha do Tempo dos últimos 6 meses
        $historicoMeses = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $nomeMes = $mes->translatedFormat('M/y');
            $mesAnoFormat = $mes->format('Y-m');

            $postsNoMes = $minhasPublicacoes->filter(function ($p) use ($mesAnoFormat) {
                return Carbon::parse($p->data_publicacao)->format('Y-m') === $mesAnoFormat;
            });

            $historicoMeses[] = [
                'mes' => ucfirst($nomeMes),
                'posts' => $postsNoMes->count(),
                'curtidas' => $postsNoMes->sum('curtidas_count'),
                'comentarios' => $postsNoMes->sum('comentarios_count')
            ];
        }

        // 6. Dados da Comunidade Scribo
        $totalMembrosComunidade = Usuario::count();
        $totalHistoriasComunidade = Publicacao::count();

        return view('metricas.index', compact(
            'usuario',
            'totalPosts',
            'totalCurtidas',
            'totalComentarios',
            'totalSalvos',
            'totalCompartilhamentos',
            'totalSeguidores',
            'totalSeguindo',
            'totalEngajamento',
            'mediaEngajamentoPorPost',
            'topPublicacoes',
            'categoriasPercentual',
            'historicoMeses',
            'totalMembrosComunidade',
            'totalHistoriasComunidade'
        ));
    }
}
