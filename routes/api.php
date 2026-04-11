<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HemocentroController;
use App\Http\Controllers\UserController;

// ROTAS DE AUTENTICAÇÃO
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ROTAS DE USUÁRIOS
Route::post('/users', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'index']);
route::get('/users/{id}', [UserController::class, 'show']);
route::put('/users/{id}', [UserController::class, 'update']);
route::delete('/users/{id}', [UserController::class, 'destroy']);

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