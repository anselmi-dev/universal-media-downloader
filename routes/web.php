<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PlatformPageController;
use Illuminate\Support\Facades\Route;

// Rutas sin localizar (API, descargas, admin)
Route::get('/download', [DownloadController::class, 'proxy'])->name('download.proxy');

// Redirigir / a /en/ o /es/ según sesión/navegador
Route::get('/', fn () => response('', 204))
    ->middleware(['localeSessionRedirect'])
    ->name('locale.redirect');

// Redirigir /dashboard y /settings a versión localizada (Fortify redirige a /dashboard)
Route::get('/dashboard', fn () => response('', 204))->middleware(['localeSessionRedirect']);
Route::get('/settings', fn () => response('', 204))->middleware(['localeSessionRedirect']);
Route::get('/settings/{path}', fn () => response('', 204))->where('path', '.*')->middleware(['localeSessionRedirect']);

// Rutas localizadas con prefijo /en/, /es/, /fr/, /de/, /pt/
Route::group([
    'prefix'     => '{locale}',
    'where'      => ['locale' => 'en|es|fr|de|pt'],
    'middleware' => ['localeFromUrl', 'localize', 'localeSessionRedirect', 'localizationRedirect'],
], function () {
    Route::view('/', 'home')->name('home');

    // Páginas dedicadas por intención de búsqueda (SEO)
    Route::get('/{platformSlug}', [PlatformPageController::class, 'show'])
        ->where('platformSlug', 'x-twitter-video-downloader|tiktok-video-downloader|instagram-downloader|reddit-video-downloader')
        ->name('platform.show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

    require __DIR__.'/settings.php';
});
