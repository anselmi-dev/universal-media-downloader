<?php

use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/download', [DownloadController::class, 'proxy'])->name('download.proxy');

Route::get('/locale/{locale}', function (string $locale) {
    $locales = array_keys(config('app.locales', ['en' => 'English', 'es' => 'Español']));
    if (in_array($locale, $locales, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
