<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHardwareToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-ESP32-API-KEY');
        $validToken = env('ESP32_API_KEY');

        if (!$token || $token !== $validToken) {
            return response()->json([
                'success' => false,
                'error' => 'unauthorized_device',
                'mensaje' => 'Dispositivo no autorizado'
            ], 401);
        }

        return $next($request);
    }
}
