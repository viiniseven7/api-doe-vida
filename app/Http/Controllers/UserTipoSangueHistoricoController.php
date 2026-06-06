<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTipoSangueHistorico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserTipoSangueHistoricoController extends Controller
{
    // GET /api/auth/doadores/{userId}/tipo-sangue-historico
    // Funcionario/Diretor: historico completo de um doador
    public function index($userId)
    {
        $user = Auth::user();

        $doador = User::findOrFail($userId);

        if ($user->role_id == 1) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        if ($user->hemocentro_id) {
            $temVinculo = $doador->triagens()
                ->where('hemocentro_id', $user->hemocentro_id)
                ->exists();

            if (!$temVinculo) {
                return response()->json(['message' => 'Acesso negado.'], 403);
            }
        }

        $historico = UserTipoSangueHistorico::with('alteradoPor:id,name')
            ->where('user_id', $userId)
            ->orderBy('alterado_em', 'desc')
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'tipo_sangue_anterior' => $h->tipo_sangue_anterior,
                'tipo_sangue_novo' => $h->tipo_sangue_novo,
                'categoria_motivo' => $h->categoria_motivo,
                'alterado_por' => $h->alteradoPor?->name,
                'alterado_em' => $h->alterado_em,
            ]);

        return response()->json([
            'status' => 'sucesso',
            'tipo_atual' => $doador->tipo_sang,
            'total_alteracoes' => $historico->count(),
            'historico' => $historico,
        ]);
    }

    // POST /api/auth/doadores/{userId}/tipo-sangue-historico
    // Funcionario altera o tipo sanguineo com registro de motivo
    public function store(Request $request, $userId)
    {
        $user = Auth::user();
        $doador = User::findOrFail($userId);

        if ($user->role_id == 1) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        if ($user->id === $doador->id) {
            return response()->json([
                'message' => 'Um profissional nao pode alterar o proprio tipo sanguineo.',
            ], 422);
        }

        if ($doador->role_id != 1) {
            return response()->json([
                'message' => 'Apenas o tipo sanguineo de doadores pode ser alterado por este endpoint.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'tipo_sangue_novo' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'categoria_motivo' => 'required|in:erro_cadastro,confirmacao_laboratorial,retificacao_com_laudo,retificacao_profissional',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($doador->tipo_sang === $request->tipo_sangue_novo) {
            return response()->json([
                'message' => 'O tipo sanguineo informado e igual ao atual. Nenhuma alteracao realizada.',
                'tipo_atual' => $doador->tipo_sang,
            ], 422);
        }

        $tipoAnterior = $doador->tipo_sang;

        DB::beginTransaction();
        try {
            UserTipoSangueHistorico::create([
                'user_id' => $doador->id,
                'tipo_sangue_anterior' => $tipoAnterior,
                'tipo_sangue_novo' => $request->tipo_sangue_novo,
                'alterado_por' => $user->id,
                'categoria_motivo' => $request->categoria_motivo,
            ]);

            $doador->update(['tipo_sang' => $request->tipo_sangue_novo]);

            Log::info('TIPO SANGUINEO ALTERADO', [
                'doador_id' => $doador->id,
                'tipo_anterior' => $tipoAnterior,
                'tipo_novo' => $request->tipo_sangue_novo,
                'categoria_motivo' => $request->categoria_motivo,
                'alterado_por' => $user->id,
                'timestamp' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ERRO AO ALTERAR TIPO SANGUINEO', [
                'erro' => $e->getMessage(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'message' => 'Erro ao alterar tipo sanguineo. Tente novamente.',
                'details' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'message' => 'Tipo sanguineo atualizado com sucesso.',
            'tipo_anterior' => $tipoAnterior,
            'tipo_novo' => $request->tipo_sangue_novo,
            'categoria' => $request->categoria_motivo,
        ], 201);
    }
}
