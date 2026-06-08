<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // IMPORTANTE

class LerJwtDoCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se existir o cookie seguro com o JWT
        if ($request->hasCookie('jwt_token')) {
            $token = $request->cookie('jwt_token');
            
            // Injeta no cabeçalho como o pacote espera
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            // FORÇA O LARAVEL A LOGAR O USUÁRIO:
            // Isso garante que Auth::id() e Auth::user() funcionem nos seus controllers!
            if ($usuario = Auth::guard('web')->user()) {
                Auth::setUser($usuario);
            }
        }

        return $next($request);
    }
}