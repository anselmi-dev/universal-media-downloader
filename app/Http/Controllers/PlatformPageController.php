<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformPageController extends Controller
{
    /**
     * Show a platform-specific landing page.
     * Overrides site config for this request so the home view renders platform content.
     */
    public function show(Request $request, string $platformSlug): View
    {
        $pages = config('platform_pages', []);
        $config = $pages[$platformSlug] ?? null;

        if (!$config) {
            abort(404);
        }

        $platform = $config['platform'];
        $site = config('site', []);

        // Merge platform-specific config into site for this request
        config([
            'site' => array_merge($site, [
                'name'        => $site['name'] ?? config('app.name'),
                'platforms'   => [$platform],
                'placeholder' => $config['placeholder'] ?? $site['placeholder'],
                'seo'         => array_merge($site['seo'] ?? [], $config['seo'] ?? []),
            ]),
        ]);

        return view('home');
    }
}
