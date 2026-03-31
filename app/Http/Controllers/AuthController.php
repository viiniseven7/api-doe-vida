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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'nullable',
            'cpf' => 'nullable',
            'bloodType' => 'nullable',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'cpf' => $validated['cpf'] ?? null,
            'blood_type' => $validated['bloodType'] ?? null,
            'role' => $validated['role'],
        ]);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou senha inválidos'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => Auth::user()
        ]);
    }
}