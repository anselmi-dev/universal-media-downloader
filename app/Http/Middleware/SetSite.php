<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Detects the current hostname and loads the matching site configuration
 * from config/sites.php into config('site.*').
 *
 * This makes the per-domain settings (allowed platforms, SEO, placeholder)
 * available everywhere (factory, layouts, views) without any changes to
 * the Livewire component.
 */
class SetSite
{
    public function handle(Request $request, Closure $next): Response
    {
        $host  = strtolower($request->getHost());
        $sites = config('sites', []);

        // Match exact hostname first, then strip leading 'www.' as fallback
        $siteConfig = $sites[$host]
            ?? $sites[preg_replace('/^www\./', '', $host)]
            ?? $sites['default']
            ?? [];

        // Publish as config('site.*') so every layer can read it
        config(['site' => $siteConfig]);

        return $next($request);
    }
}
