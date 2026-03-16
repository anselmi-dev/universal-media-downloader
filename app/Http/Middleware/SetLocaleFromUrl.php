<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\Response;

/**
 * Llama a LaravelLocalization::setLocale() para que el paquete
 * establezca el locale desde el segmento de la URL.
 */
class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        LaravelLocalization::setLocale();

        return $next($request);
    }
}
