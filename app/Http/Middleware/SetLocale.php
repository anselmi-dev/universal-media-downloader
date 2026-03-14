<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        // 1. User explicitly chose a locale via the switcher → always respect it
        $sessionLocale = $request->session()->get('locale');

        if ($sessionLocale && in_array($sessionLocale, self::SUPPORTED, true)) {
            App::setLocale($sessionLocale);

            return $next($request);
        }

        // 2. No manual choice yet → auto-detect from the browser's Accept-Language header
        //    getPreferredLanguage() handles regional variants (es-MX → es, en-US → en)
        //    and falls back to the app default when nothing matches.
        $detected = $request->getPreferredLanguage(self::SUPPORTED);

        if ($detected) {
            App::setLocale($detected);
        }

        return $next($request);
    }
}
