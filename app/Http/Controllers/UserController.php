<?php

namespace App\Http\Controllers; // 🔥 ISSO FALTAVA

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // 📋 LISTAR
    public function index() {
        return User::all();
    }

    // 🔍 VER UM
    public function show($id) {
        return User::findOrFail($id);
    }

    // ➕ CRIAR (DIRETOR)
    public function store(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'cpf' => 'required|unique:users,cpf'
        ]);

        if (!$this->validarCPF($request->cpf)) {
            return response()->json([
                'error' => 'CPF inválido'
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf' => $request->cpf,
        ]);

        // 🔥 DEFINE ROLE AUTOMÁTICO
        $user->assignRole('funcionario');

        return response()->json([
            'message' => 'Funcionário criado com sucesso',
            'user' => $user
        ]);
    }

    // ✏️ ATUALIZAR (ADMIN)
    public function update($id, Request $request) {

        $user = User::findOrFail($id);

        // atualiza dados básicos
        $user->update($request->only(['name', 'email']));

        // 🔐 se vier senha → criptografa
        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // 🔥 ALTERAR ROLE (SE ADMIN)
        if ($request->role) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user
        ], 200 );
    }

    // 🗑 INATIVAR
    public function destroy($id) {

        $user = User::findOrFail($id);
        $user->status = 0;
        $user->save();

        return response()->json([
            'message' => 'Usuário inativado com sucesso'
        ]);
    }

    // 🔎 VALIDAR CPF
    private function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) return false;
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) return false;
        }

        return true;
    }
}