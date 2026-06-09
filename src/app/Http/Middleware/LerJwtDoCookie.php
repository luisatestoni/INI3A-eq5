<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LerJwtDoCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se houver um cookie com o token
        if ($token = $request->cookie('token')) {
            
            // Injeta o token no cabeçalho para o pacote JWT interceptar
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            // Verifica se o Guard de API (JWT) consegue autenticar com esse token
            if (Auth::guard('api')->check()) {
                $usuario = Auth::guard('api')->user();
                
                // Força o Laravel a reconhecer o usuário na sessão web também
                Auth::guard('web')->login($usuario);
            }
        }

        return $next($request);
    }
}