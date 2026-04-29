<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\TriagemController;
use App\Http\Controllers\EmailVerificationController;

// ======================================================
// 🔓 ROTAS PÚBLICAS
// ======================================================
Route::prefix('auth')->group(function () {
    Route::post('/register',       [AuthController::class, 'register']);
    Route::post('/login',          [AuthController::class, 'login']);
    Route::post('/forgot-password',[AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Leitura pública (conforme doc da API)
Route::get('/users',                    [UserController::class, 'index']);
Route::get('/users/{id}',               [UserController::class, 'show']);
Route::get('/hemocentros',              [HemocentroController::class, 'index']);
Route::get('/hemocentros/{hemocentro}', [HemocentroController::class, 'show']);

// ======================================================
// 🔐 ROTAS PROTEGIDAS
// ======================================================
Route::middleware('auth:sanctum')->group(function () {

    // Usuário logado
    Route::get('/auth/me', function (Request $request) {
        return response()->json([
            'user'  => $request->user(),
            'roles' => $request->user()->getRoleNames(),
        ]);
    });

    // Email
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1');

    // ==================================================
    // 👤 USUÁRIOS — escrita protegida por role_id
    // ==================================================
    Route::put('/users/{id}',    [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::prefix('auth')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
    });

    // ==================================================
    // 🏥 HEMOCENTROS — escrita protegida
    // ==================================================
    Route::prefix('auth')->group(function () {
        Route::post('/hemocentros',               [HemocentroController::class, 'store']);
        Route::put('/hemocentros/{hemocentro}',   [HemocentroController::class, 'update']);
        Route::delete('/hemocentros/{hemocentro}',[HemocentroController::class, 'destroy']);
    });

    // ==================================================
    // 📅 AGENDAMENTOS
    // ==================================================

    // Doador vê os seus; funcionário/diretor/admin veem todos
    // O controller decide o filtro baseado em role_id
    Route::get('/agendamentos', [AgendamentoController::class, 'index']);

    Route::prefix('auth')->group(function () {
        Route::post('/agendamentos',        [AgendamentoController::class, 'store']);
        Route::put('/agendamentos/{id}',    [AgendamentoController::class, 'update']);
        Route::delete('/agendamentos/{id}', [AgendamentoController::class, 'destroy']);
    });

    // Dashboard do doador
    Route::get('/me/dashboard', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });

    // ==================================================
    // 🩺 TRIAGEM
    // ==================================================
    Route::get('/triagens', [TriagemController::class, 'index']);
    Route::prefix('auth')->group(function () {
        Route::post('/triagens',        [TriagemController::class, 'store']);
        Route::put('/triagens/{id}',    [TriagemController::class, 'update']);
        Route::delete('/triagens/{id}', [TriagemController::class, 'destroy']);
    });
});