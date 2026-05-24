<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Doacao;
use App\Models\Estoque;
use App\Models\User;
use App\Models\Hemocentro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    private const TIPOS_SANGUINEOS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    /**
     * JSON: Resumo de doações por status para dashboard.
     */
    public function donationsSummary(Request $request)
    {
        $hemocentroId = $this->getHemocentroId($request);
        $periodo = $request->query('dias', 30);

        $query = Agendamento::query()
            ->select('status_agendamento', DB::raw('COUNT(*) as total'))
            ->where('criado_em', '>=', Carbon::now()->subDays($periodo))
            ->groupBy('status_agendamento');

        if ($hemocentroId) {
            $query->where('hemocentro_id', $hemocentroId);
        }

        $resumo = $query->pluck('total', 'status_agendamento')->toArray();

        // Mapeamento de status para labels legíveis
        $labels = [
            'AGE' => 'Agendado',
            'CAN' => 'Cancelado',
            'CON' => 'Concluído',
            'EXC' => 'Excedido/Substituído'
        ];

        $data = [];
        foreach ($labels as $status => $label) {
            $data[] = [
                'label' => $label,
                'total' => (int) ($resumo[$status] ?? 0)
            ];
        }

        return response()->json($data);
    }

    /**
     * JSON: Saldo de estoque por tipo sanguíneo.
     */
    public function bloodStock(Request $request)
    {
        $hemocentroId = $this->getHemocentroId($request);

        $query = Estoque::query()
            ->select('tipo_sangue', DB::raw('SUM(quantidade) as total'))
            ->groupBy('tipo_sangue');

        if ($hemocentroId) {
            $query->where('hemocentro_id', $hemocentroId);
        }

        $estoque = $query->pluck('total', 'tipo_sangue')->toArray();

        $data = [];
        foreach (self::TIPOS_SANGUINEOS as $tipo) {
            $data[] = [
                'tipo' => $tipo,
                'quantidade' => (float) ($estoque[$tipo] ?? 0)
            ];
        }

        return response()->json($data);
    }

    /**
     * JSON: Desempenho mensal (últimos 12 meses).
     */
    public function performanceMonthly(Request $request)
    {
        $hemocentroId = $this->getHemocentroId($request);

        $query = Doacao::query()
            ->selectRaw($this->monthExpression() . ' as mes, COUNT(*) as total')
            ->where('data_hora_doacao', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes');

        if ($hemocentroId) {
            $query->where('hemocentro_id', $hemocentroId);
        }

        $data = $query->get()->map(fn ($item) => [
            'mes' => $item->mes,
            'total' => (int) $item->total
        ]);

        return response()->json($data);
    }

    /**
     * PDF: Relatório de Doações.
     */
    public function pdfDoacoes(Request $request)
    {
        $user = Auth::user();
        $hemocentroId = $this->getHemocentroId($request);
        $periodo = $request->query('periodo', 30);

        $query = Doacao::with(['doador', 'funcionario', 'hemocentro'])
            ->where('data_hora_doacao', '>=', Carbon::now()->subDays($periodo))
            ->orderBy('data_hora_doacao', 'desc');

        if ($hemocentroId) {
            $query->where('hemocentro_id', $hemocentroId);
        }

        $doacoes = $query->get();

        $pdf = Pdf::loadView('relatorios.doacoes', [
            'doacoes'   => $doacoes,
            'periodo'   => $periodo,
            'gerado_em' => now()->format('d/m/Y H:i'),
            'unidade'   => $hemocentroId ? Hemocentro::find($hemocentroId)->nome : 'Todas as Unidades'
        ]);

        return $pdf->download('relatorio-doacoes.pdf');
    }

    /**
     * PDF: Relatório de Estoque.
     */
    public function pdfEstoque(Request $request)
    {
        $hemocentroId = $this->getHemocentroId($request);

        $query = Estoque::with('hemocentro');
        if ($hemocentroId) {
            $query->where('hemocentro_id', $hemocentroId);
        }

        $estoque = $query->get();

        $pdf = Pdf::loadView('relatorios.estoque', [
            'estoque'   => $estoque,
            'gerado_em' => now()->format('d/m/Y H:i'),
            'unidade'   => $hemocentroId ? Hemocentro::find($hemocentroId)->nome : 'Global'
        ]);

        return $pdf->download('relatorio-estoque.pdf');
    }

    /**
     * PDF: Relatório de Doadores.
     */
    public function pdfDoadores(Request $request)
    {
        $hemocentroId = $this->getHemocentroId($request);

        $query = User::where('role_id', 1) // 1 = Doador
            ->orderBy('name');

        if ($hemocentroId) {
            // Filtra doadores que já interagiram com este hemocentro
            $query->whereHas('triagens', function ($q) use ($hemocentroId) {
                $q->where('hemocentro_id', $hemocentroId);
            });
        }

        $doadores = $query->get();

        $pdf = Pdf::loadView('relatorios.doadores', [
            'doadores'  => $doadores,
            'gerado_em' => now()->format('d/m/Y H:i'),
            'unidade'   => $hemocentroId ? Hemocentro::find($hemocentroId)->nome : 'Geral'
        ]);

        return $pdf->download('relatorio-doadores.pdf');
    }

    private function getHemocentroId(Request $request): ?int
    {
        $user = Auth::user();
        
        // Se for admin, ele pode passar um hemocentro_id via query para filtrar, senão vê tudo
        if ($user->role_id == 4) { // Admin
            return $request->query('hemocentro_id');
        }

        // Para Diretor e Funcionário, fixa o ID do hemocentro deles
        return $user->hemocentro_id;
    }

    private function monthExpression(): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', data_hora_doacao)"
            : "DATE_FORMAT(data_hora_doacao, '%Y-%m')";
    }
}
