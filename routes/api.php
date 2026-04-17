<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PasswordResetController;

// ROTAS DE AUTENTICAÇÃO
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// 🔥 ROTA PÚBLICA (AGENDAMENTO)
// 👉 ESSA FOI A CORREÇÃO PRINCIPAL
Route::post('/users', [UserController::class, 'store']);


// 🔴 DIRETOR (gerencia usuários)
Route::middleware(['auth:sanctum', 'role:diretor'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);      // listar
    Route::get('/users/{id}', [UserController::class, 'show']);  // ver
    // Route::post('/users', [UserController::class, 'store']);  // ❌ removido daqui
});


// 🔵 ADMIN (controle total)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::put('/users/{id}', [UserController::class, 'update']);   // editar
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // deletar
});


// ESQUECI SENHA
Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);


//GRUPO DE ROTAS PROTEGIDAS POR AUTENTICAÇÃO - 
//  route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function() {
//     route::get('/', [UserController::class, 'index']);
//     route::get('/{id}', [UserController::class, 'show']);
//     route::put('/{id}', [UserController::class, 'update']);
//     route::delete('/{id}', [UserController::class, 'destroy']);
// });


// ROTAS PARA OS HEMOCENTROS
Route::get   ('/hemocentros',         [HemocentroController::class, 'index']);
Route::get   ('/hemocentros/{id}',    [HemocentroController::class, 'show']);
Route::post  ('/hemocentros',         [HemocentroController::class, 'store']);
Route::put   ('/hemocentros/{id}',    [HemocentroController::class, 'update']);
Route::delete('/hemocentros/{id}',    [HemocentroController::class, 'destroy']);


// (opcional - se ainda quiser usar)
// Route::post('/appointments', [AppointmentController::class, 'store']);