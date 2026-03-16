<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordVisits
{
    private const EXCLUDED_PATHS = [
        '/admin',
        '/download',
        '/locale/',
        '/up',
        '/_ignition',
        '/livewire',
        '/favicon',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldRecord($request, $response)) {
            $this->record($request);
        }

        return $response;
    }

    private function shouldRecord(Request $request, Response $response): bool
    {
        if ($request->method() !== 'GET') {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $path = '/' . ($request->path() ?: '');

        foreach (self::EXCLUDED_PATHS as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return false;
            }
        }

        return true;
    }

    private function record(Request $request): void
    {
        try {
            PageVisit::create([
                'path' => $request->path() ? '/' . $request->path() : '/',
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('Referer'),
                'site_host' => $request->getHost(),
                'locale' => app()->getLocale(),
            ]);
        } catch (\Throwable) {
            // Silently ignore
        }
    }
}
