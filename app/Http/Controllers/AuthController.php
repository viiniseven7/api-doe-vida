<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed',
            'cpf'       => 'required|string|size:14|unique:users,cpf',
            'telefone'  => 'nullable|string|max:20|regex:/^\(\d{2}\)\s9\d{4}-\d{4}$/',
            'tipo_sang' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'sexo'      => 'required|in:M,F,Outro,Prefiro não informar',
            'data_nasc' => 'required|date_format:d/m/Y',
            'cep'       => 'required|regex:/^\d{5}-?\d{3}$/',
            'rua'       => 'required|string|max:255',
            'numero'    => 'required|string|max:10',
            'bairro'    => 'nullable|string|max:255',
            'cidade'    => 'required|string|max:255',
            'uf'        => 'nullable|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'hemocentro_id' => 'required|exists:hemocentro,id',

            'responsavel_nome' => 'nullable|string|max:255',
            'responsavel_cpf' => 'nullable|string|size:14|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/|unique:users,responsavel_cpf',
            'responsavel_data_nasc' => 'nullable|date_format:d/m/Y',
            'responsavel_telefone' => 'nullable|string|max:20|regex:/^\(\d{2}\)\s9\d{4}-\d{4}$/',
        ]);

        try {
            // 📅 DATA + IDADE
            $dataNasc = Carbon::createFromFormat('d/m/Y', $validated['data_nasc']);
            $idade = $dataNasc->age;

            // 🔎 VALIDAR CPF
            if (!$this->validarCPF($validated['cpf'])) {
                return response()->json([
                    'error' => 'CPF inválido.'
                ], 422);
            }

            // 🚫 BLOQUEAR <16
            if ($idade < 16) {
                return response()->json([
                    'error' => 'Cadastro permitido apenas para maiores de 16 anos.'
                ], 422);
            }

            // ⚠️ 16–17 → responsável obrigatório
            if ($idade >= 16 && $idade <= 17) {

                if (
                    empty($validated['responsavel_nome']) ||
                    empty($validated['responsavel_cpf']) ||
                    empty($validated['responsavel_data_nasc']) ||
                    empty($validated['responsavel_telefone'])
                ) {
                    return response()->json([
                        'error' => 'Usuários entre 16 e 17 anos precisam de responsável.'
                    ], 422);
                }

                if (!$this->validarCPF($validated['responsavel_cpf'])) {
                    return response()->json([
                        'error' => 'CPF do responsável inválido.'
                    ], 422);
                }

                $dataResp = Carbon::createFromFormat('d/m/Y', $validated['responsavel_data_nasc']);

                if ($dataResp->age < 18) {
                    return response()->json([
                        'error' => 'Responsável deve ser maior de idade.'
                    ], 422);
                }
            }

            // 🧹 LIMPAR responsável se for maior de 18
            if ($idade >= 18) {
                $validated['responsavel_nome'] = null;
                $validated['responsavel_cpf'] = null;
                $validated['responsavel_data_nasc'] = null;
                $validated['responsavel_telefone'] = null;
            }

            // ✅ CRIAR USUÁRIO
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'cpf'       => $validated['cpf'],
                'telefone'  => $validated['telefone'] ?? null,
                'tipo_sang' => $validated['tipo_sang'] ?? null,
                'sexo'      => $validated['sexo'],
                'data_nasc' => $dataNasc->format('Y-m-d'),
                'cep'       => $validated['cep'],
                'rua'       => $validated['rua'],
                'numero'    => $validated['numero'],
                'bairro'    => $validated['bairro'] ?? null,
                'cidade'    => $validated['cidade'],
                'uf'        => $validated['uf'] ?? null,
                'hemocentro_id' => $validated['hemocentro_id'],
                
                'status'    => true,

                'responsavel_nome' => $validated['responsavel_nome'] ?? null,
                'responsavel_cpf'  => $validated['responsavel_cpf'] ?? null,
                'responsavel_telefone' => $validated['responsavel_telefone'] ?? null,
                'responsavel_data_nasc' => !empty($validated['responsavel_data_nasc'])
                    ? Carbon::createFromFormat('d/m/Y', $validated['responsavel_data_nasc'])->format('Y-m-d')
                    : null,
            ]);

            // 🔥 ROLE PADRÃO
            $user->assignRole('doador');

            return response()->json([
    'message' => 'Doador registrado com sucesso!',
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->getRoleNames()->first(),
    ]
], 201);

        } catch (\Exception $e) {
          return response()->json([
    'error' => $e->getMessage()
], 500);
        }
    }

    // 🔐 LOGIN
    public function login(Request $request)
{
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

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login realizado com sucesso!',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
        ],
        'token' => $token,
        'token_type' => 'Bearer',
    ]);
}

    // 🗑 DELETE
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Usuário não encontrado'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuário inativado com sucesso'
        ]);
    }

    // 🔎 VALIDAR CPF
    private function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}