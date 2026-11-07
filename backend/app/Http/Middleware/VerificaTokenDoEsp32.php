<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Só aceita a requisição se o header X-Device-Token for igual ao ESP32_TOKEN do .env.
 */
class VerificaTokenDoEsp32
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $esperado = (string) config('services.esp32.token');
        $recebido = (string) $request->header('X-Device-Token');

        abort_if($esperado === '' || ! hash_equals($esperado, $recebido), 401, 'Token do ESP32 inválido.');

        return $next($request);
    }
}
