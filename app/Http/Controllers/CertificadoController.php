<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificadoController extends Controller
{
    /**
     * Lista as doações concluídas do doador logado que podem gerar certificado.
     */
    public function index()
    {
        $user = Auth::user();

        // Um certificado é baseado em uma doação realizada com sucesso
        $doacoes = Doacao::with(['hemocentro'])
            ->where('user_id', $user->id)
            ->orderBy('data_hora_doacao', 'desc')
            ->get();

        return response()->json([
            'status' => 'sucesso',
            'data' => $doacoes
        ]);
    }

    /**
     * Gera o PDF do certificado para uma doação específica.
     */
    public function download($id)
    {
        $user = Auth::user();
        
        $doacao = Doacao::with(['doador', 'hemocentro'])
            ->where('id', $id)
            ->where('user_id', $user->id) // Garante que o doador só baixe o próprio certificado
            ->firstOrFail();

        $pdf = Pdf::loadView('relatorios.certificado', [
            'doacao' => $doacao
        ])->setPaper('a4', 'landscape');

        return $pdf->download("certificado-doacao-{$doacao->id}.pdf");
    }
}
