<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    /**
     * Exibe a página completa com todas as notificações do usuário
     */
    public function listar(Request $request)
    {
        $usuario = Auth::user();

        $filtro = $request->query('filtro', 'todas');

        if ($filtro === 'nao-lidas') {
            $notificacoes = $usuario->unreadNotifications()->paginate(15);
        } else {
            $notificacoes = $usuario->notifications()->paginate(15);
        }

        return view('notificacoes.index', compact('notificacoes', 'filtro'));
    }

    /**
     * Retorna as últimas notificações em formato JSON para o menu dropdown do cabeçalho
     */
    public function ultimas()
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return response()->json([
                'total_nao_lidas' => 0,
                'notificacoes' => []
            ]);
        }

        $totalNaoLidas = $usuario->unreadNotifications()->count();

        $notificacoes = $usuario->notifications()
            ->take(8)
            ->get()
            ->map(function ($notificacao) {
                return [
                    'id'            => $notificacao->id,
                    'lida'          => !is_null($notificacao->read_at),
                    'tipo'          => $notificacao->data['tipo'] ?? 'geral',
                    'autor_id'      => $notificacao->data['autor_id'] ?? null,
                    'autor_nome'    => $notificacao->data['autor_nome'] ?? 'Alguém',
                    'autor_username'=> $notificacao->data['autor_username'] ?? '',
                    'autor_foto'    => !empty($notificacao->data['autor_foto']) 
                                        ? asset('storage/' . $notificacao->data['autor_foto']) 
                                        : asset('imagens/perfil-v1.png'),
                    'mensagem'      => $notificacao->data['mensagem'] ?? '',
                    'titulo_post'   => $notificacao->data['titulo_post'] ?? null,
                    'url'           => route('notificacoes.ler', $notificacao->id),
                    'tempo'         => $notificacao->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'total_nao_lidas' => $totalNaoLidas,
            'notificacoes' => $notificacoes
        ]);
    }

    /**
     * Marca uma única notificação como lida e redireciona para a URL de destino
     */
    public function marcarUmaComoLida($id)
    {
        $usuario = Auth::user();
        $notificacao = $usuario->notifications()->where('id', $id)->first();

        if ($notificacao) {
            $notificacao->markAsRead();
            $urlDestino = $notificacao->data['url'] ?? route('feed');

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['sucesso' => true, 'url' => $urlDestino]);
            }

            return redirect($urlDestino);
        }

        return redirect()->route('feed');
    }

    /**
     * Marca todas as notificações do usuário como lidas
     */
    public function marcarLidas()
    {
        $usuario = Auth::user();

        if ($usuario) {
            $usuario->unreadNotifications->markAsRead();
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['sucesso' => true]);
        }

        return redirect()->back()->with('sucesso', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Remove notificações lidas
     */
    public function limpar()
    {
        $usuario = Auth::user();

        if ($usuario) {
            $usuario->readNotifications()->delete();
        }

        return redirect()->route('notificacoes.listar')->with('sucesso', 'Notificações lidas foram removidas.');
    }
}