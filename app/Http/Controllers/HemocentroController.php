<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hemocentro;

class HemocentroController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'               => 'required|string|max:255',
            'telefone'           => 'required|string|max:20',
            'email'              => 'required|email|max:255|unique:hemocentro,email',
            'bairro'             => 'required|string|max:255',
            'uf'                 => 'required|string|max:2',
            'endereco'           => 'required|string|max:255',
            'cidade'             => 'required|string|max:255',
            'numero'             => 'required|integer',
            'complemento'        => 'nullable|string|max:255', // Nullable pois nem todo endereço tem
            'razao_social'       => 'required|string|max:255',
            'cnpj'               => 'required|string|max:20|unique:hemocentro,cnpj', // Adicionado unique para não repetir CNPJ
            'status_agendamento' => 'required|in:ativo,inativo',
            'status'             => 'required|integer|in:0,1', // Garante que seja apenas 0 ou 1 (tinyint)
            'criado_por'         => 'nullable|string|max:255' // Pode ser preenchido pela API ou opcional
        ]);

        $hemocentro = Hemocentro::create($validated);

        return response()->json([
            'message' => 'Hemocentro criado com sucesso!',
            'data' => $hemocentro
        ], 201);
    }
    
    // Aproveitando, aqui está o método para listar todos, que o Front-end vai precisar:
    public function index()
    {
        $hemocentros = Hemocentro::all();
        return response()->json($hemocentros, 200);
    }
}

