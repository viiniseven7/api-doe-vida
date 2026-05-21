<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\AlertaMedicoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\EstatisticaController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\TriagemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTipoSangueHistoricoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/hemocentros', [HemocentroController::class, 'index']);
Route::get('/hemocentros/{hemocentro}', [HemocentroController::class, 'show']);
Route::get('/triagens/perguntas', [TriagemController::class, 'perguntas']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'roles' => $request->user()->getRoleNames(),
        ]);
    });
    Route::delete('/auth/minha-conta', [AuthController::class, 'excluirConta']);
    Route::get('/auth/meus-dados', [AuthController::class, 'meusDados']);
    Route::post('/auth/elegibilidade', [AuthController::class, 'salvarElegibilidade']);
    Route::get('/auth/elegibilidade/atual', [AuthController::class, 'getElegibilidade']);

    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::prefix('auth')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/hemocentros', [HemocentroController::class, 'store']);
        Route::put('/hemocentros/{hemocentro}', [HemocentroController::class, 'update']);
        Route::delete('/hemocentros/{hemocentro}', [HemocentroController::class, 'destroy']);
    });

    Route::get('/agendamentos', [AgendamentoController::class, 'index']);
    Route::get('/agendamentos/historico', [AgendamentoController::class, 'historico']);
    Route::get('/agendamentos/{id}', [AgendamentoController::class, 'show']);

    Route::prefix('auth')->group(function () {
        Route::post('/agendamentos', [AgendamentoController::class, 'store']);
        Route::post('/agendamentos/{id}/confirmar', [AgendamentoController::class, 'confirmar']);
        Route::post('/agendamentos/{id}/cancelar', [AgendamentoController::class, 'cancelar']);
        Route::delete('/agendamentos/{id}', [AgendamentoController::class, 'destroy']);
    });

    Route::get('/triagens', [TriagemController::class, 'index']);
    Route::get('/triagens/{id}', [TriagemController::class, 'show']);

    Route::prefix('auth')->group(function () {
        Route::post('/triagens', [TriagemController::class, 'store']);
        Route::put('/triagens/{id}', [TriagemController::class, 'update']);
        Route::delete('/triagens/{id}', [TriagemController::class, 'destroy']);
    });

    Route::get('/alertas-medicos', [AlertaMedicoController::class, 'index']);
    Route::get('/alertas-medicos/{id}', [AlertaMedicoController::class, 'show']);

    Route::prefix('auth')->group(function () {
        Route::post('/alertas-medicos', [AlertaMedicoController::class, 'store']);
        Route::put('/alertas-medicos/{id}', [AlertaMedicoController::class, 'update']);
        Route::delete('/alertas-medicos/{id}', [AlertaMedicoController::class, 'destroy']);
    });

    Route::prefix('auth')->group(function () {
        Route::get('/doadores/{userId}/tipo-sangue-historico', [UserTipoSangueHistoricoController::class, 'index']);
        Route::post('/doadores/{userId}/tipo-sangue-historico', [UserTipoSangueHistoricoController::class, 'store']);
    });

    Route::get('/doacoes', [DoacaoController::class, 'index']);
    Route::get('/doacoes/{id}', [DoacaoController::class, 'show']);

    Route::get('/estoque', [EstoqueController::class, 'index']);
    Route::get('/estoque/{id}', [EstoqueController::class, 'show']);

    Route::get('/estatisticas/funcionario', [EstatisticaController::class, 'funcionario']);
    Route::get('/estatisticas/diretor', [EstatisticaController::class, 'diretor']);
    Route::get('/estatisticas/admin', [EstatisticaController::class, 'admin']);

    Route::get('/certificados', [\App\Http\Controllers\CertificadoController::class, 'index']);
    Route::get('/certificados/{id}/pdf', [\App\Http\Controllers\CertificadoController::class, 'download']);

    Route::prefix('auth')->group(function () {
        Route::post('/doacoes', [DoacaoController::class, 'store']);
        Route::post('/estoque', [EstoqueController::class, 'store']);
        Route::put('/estoque/{id}', [EstoqueController::class, 'update']);
    });
});
