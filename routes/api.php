<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\TriagemController;
use App\Http\Controllers\EmailVerificationController;

// ======================================================
// 🔓 ROTAS PÚBLICAS (SEM LOGIN)
// ======================================================
Route::prefix('auth')->group(function () {

    // 🔐 Auth básico
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // 🔑 Recuperação de senha
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


// ======================================================
// 🔐 ROTAS PROTEGIDAS (AUTH)
// ======================================================
Route::middleware('auth:sanctum')->group(function () {

    // --------------------------------------------------
    // 👤 USUÁRIO LOGADO
    // --------------------------------------------------
    Route::get('/auth/me', function (Request $request) {
        return response()->json([
            'user'  => $request->user(),
            'roles' => $request->user()->getRoleNames()
        ]);
    });

    // ✅ Verificação de email
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1');

    // ==================================================
    // 🩸 DOADOR
    // ==================================================
    Route::middleware('role:Doador')->group(function () {

        // ✅ Dashboard do doador (estava fora do auth:sanctum)
        Route::get('/me/dashboard', function (Request $request) {
            return response()->json([
                'user'          => $request->user(),
                'total_doacoes' => $request->user()->agendamentos()->count(),
            ]);
        });

        Route::get('/me/doacoes', function (Request $request) {
            return $request->user()->agendamentos;
        });

        Route::post('/agendamentos', [AgendamentoController::class, 'store']);
        Route::get('/me/agendamentos', [AgendamentoController::class, 'meus']);
        Route::put('/agendamentos/{id}', [AgendamentoController::class, 'update']);
        Route::delete('/agendamentos/{id}', [AgendamentoController::class, 'destroy']);
    });

    // ==================================================
    // 👤 USUÁRIOS
    // ==================================================

    // 🔴 ADMIN + FUNCIONÁRIO → cria e deleta
    Route::middleware('role:Admin|Funcionario')->group(function () {
        Route::post('/auth/users', [UserController::class, 'store']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // 🟡 DIRETOR → atualiza
    Route::middleware('role:Diretor')->group(function () {
        Route::put('/users/{id}', [UserController::class, 'update']);
    });

    // 🔵 VISUALIZAÇÃO
    Route::middleware('role:Funcionario|Diretor|Admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
    });

    // ==================================================
    // 🏥 HEMOCENTROS
    // ==================================================

    // 🔴 ADMIN + DIRETOR → CRUD
    Route::middleware('role:Admin|Diretor')->group(function () {
        Route::post('/hemocentros', [HemocentroController::class, 'store']);
        Route::put('/hemocentros/{hemocentro}', [HemocentroController::class, 'update']);
        Route::delete('/hemocentros/{hemocentro}', [HemocentroController::class, 'destroy']);
    });

    // 🔓 TODOS LOGADOS → leitura
    Route::get('/hemocentros', [HemocentroController::class, 'index']);
    Route::get('/hemocentros/{hemocentro}', [HemocentroController::class, 'show']);

    // ==================================================
    // 📅 AGENDAMENTOS
    // ==================================================

    // 🔵 FUNCIONÁRIO / DIRETOR / ADMIN → visualizar todos
    Route::middleware('role:Funcionario|Diretor|Admin')->group(function () {
        Route::get('/agendamentos', [AgendamentoController::class, 'index']);
    });

    // ==================================================
    // 🩺 TRIAGEM
    // ==================================================
    Route::middleware('role:Funcionario|Diretor')->group(function () {
        Route::apiResource('triagens', TriagemController::class);
    });

});