<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|min:6|confirmed', 
        'cpf'       => 'required|string|max:14|unique:users,cpf', // Validação de unicidade
        'telefone'  => 'nullable|string|max:20',
        'tipo_sang' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
    ]);

        $user = User::create([
        'name'      => $validated['name'],
        'email'     => $validated['email'],
        'password'  => bcrypt($validated['password']),
        'cpf'       => $validated['cpf'],
        'telefone'  => $validated['telefone'],
        'tipo_sang' => $validated['tipo_sang'],
        
        // REGRA DE NEGÓCIO: Todo registro via site/app é Doador (ID 1 = doador)
        'role_id'   => 1, 
        'status'    => true,
    ]);

        return response()->json([
        'message' => 'Doador registrado com sucesso!',
        'user'    => $user
    ], 201);
}

    public function login(Request $request)
{
    // 1. Validação (Sempre valide o que vem do usuário!)
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'E-mail ou senha inválidos.'
        ], 401);
    }

    $user = Auth::user();

    // Isso usa o Laravel Sanctum, que já veio naquelas tabelas automáticas (personal_access_tokens)
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login realizado com sucesso!',
        'user'    => $user,
        'token'   => $token, // O Front-end guarda esse token para as próximas chamadas
        'token_type' => 'Bearer',
    ]);
 }
} 