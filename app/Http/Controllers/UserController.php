<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'cpf'           => 'required|string|max:14|unique:users,cpf',
            'role'          => 'required|string|exists:roles,name',
            'hemocentro_id' => [
                Rule::requiredIf(fn() => in_array($request->role, ['funcionario', 'diretor'])),
                'nullable',
                'exists:hemocentros,id'
            ],
            'telefone'      => 'nullable|string|max:20',
            'tipo_sang'     => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'sexo'          => 'nullable|in:M,F,Outro,Prefiro não informar',
            'data_nasc'     => 'nullable|date_format:d/m/Y',
        ]);

        // Valida CPF
        if (!$this->validarCPF($validated['cpf'])) {
            return response()->json(['error' => 'CPF inválido'], 422);
        }

        if ($validated['role'] === 'doador') {
            $validated['hemocentro_id'] = null;
        }

        if (!empty($validated['data_nasc'])) {
            $validated['data_nasc'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['data_nasc'])->format('Y-m-d');
        }

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'cpf'           => $validated['cpf'],
            'hemocentro_id' => $validated['hemocentro_id'] ?? null,
            'telefone'      => $validated['telefone'] ?? null,
            'tipo_sang'     => $validated['tipo_sang'] ?? null,
            'sexo'          => $validated['sexo'] ?? null,
            'data_nasc'     => $validated['data_nasc'] ?? null,
            'status'        => true,
            'criado_por'    => $request->user()->id
        ]);

        $user->assignRole($validated['role']);

        return response()->json($user, 201);
    }

    public function index()
    {
        return User::all();
    }

    public function show(int $id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:users,email,' . $id,
            'password'      => 'sometimes|min:6',
            'cpf'           => 'sometimes|string|max:14|unique:users,cpf,' . $id,
            'role'          => 'sometimes|string|exists:roles,name',
            'hemocentro_id' => [
                Rule::requiredIf(function() use ($request, $user) {
                    $role = $request->role ?? $user->getRoleNames()->first();
                    return in_array($role, ['funcionario', 'diretor']);
                }),
                'nullable',
                'exists:hemocentros,id'
            ],
            'status'        => 'sometimes|boolean',
            'tipo_sang'     => 'sometimes|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'sexo'          => 'sometimes|in:M,F,Outro,Prefiro não informar',
            'telefone'      => 'sometimes|string|max:20',
            'data_nasc'     => 'sometimes|date_format:d/m/Y',
        ]);

        if (isset($validated['cpf']) && !$this->validarCPF($validated['cpf'])) {
            return response()->json(['error' => 'CPF inválido'], 422);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if (!empty($validated['data_nasc'])) {
            $validated['data_nasc'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['data_nasc'])->format('Y-m-d');
        }

        $finalRole = $validated['role'] ?? $user->getRoleNames()->first();

        if ($finalRole === 'doador') {
            $validated['hemocentro_id'] = null;
        }

        $role = $validated['role'] ?? null;
        unset($validated['role']);

        $user->update($validated);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user->fresh()
        ], 200);
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        $user->status = false;
        $user->save();
        $user->delete();

        return response()->json([
            'message' => 'Usuário inativado e removido com sucesso'
        ]);
    }

    private function validarCPF(string $cpf): bool
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