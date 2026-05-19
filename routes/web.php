<?php

use App\Models\Hemocentro;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $hemocentros = Hemocentro::query()
        ->where('status', 1)
        ->orderBy('cidade')
        ->orderBy('nome')
        ->get();

    return view('minha-pagina', compact('hemocentros'));
});

Route::get('/hemocentros', function () {
    $hemocentros = Hemocentro::query()
        ->where('status', 1)
        ->orderBy('cidade')
        ->orderBy('nome')
        ->get();

    return view('minha-pagina', compact('hemocentros'));
});

Route::get('/logs', function () {
    abort_unless(app()->environment('local') || config('app.debug'), 403);

    $path = storage_path('logs/laravel.log');
    $content = File::exists($path) ? File::get($path) : '';
    $lines = $content === '' ? [] : preg_split('/\R/', trim($content));
    $lines = array_slice($lines ?: [], -300);

    return view('logs', [
        'lines' => $lines,
        'path' => $path,
    ]);
})->name('logs.index');
