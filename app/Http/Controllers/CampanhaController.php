<?php
namespace App\Http\Controllers;

use App\Models\Campanha;
use App\Models\Doacao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CampanhaController extends Controller
{
    // GET /api/campanhas
    public function index(Request $request)
    {
        $campanhas = Campanha::with('hemocentro')
            ->orderBy('criado_em', 'desc')
            ->get();

        return response()->json([
            'status' => 'sucesso',
            'data'   => $campanhas,
        ]);
    }

    // GET /api/campanhas/{id}
    public function show($id)
    {
        $campanha = Campanha::with('hemocentro', 'criador')->findOrFail($id);
        return response()->json(['status' => 'sucesso', 'data' => $campanha]);
    }

    // POST /api/auth/campanhas
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'         => 'required|string|max:255',
            'subtitulo'      => 'nullable|string|max:255',
            'descricao'      => 'nullable|string',
            'tipo_sangue'    => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'hemocentro_id'  => 'nullable|exists:hemocentros,id',
            'data_publi'     => 'required|date',
            'data_expiracao' => 'nullable|date|after:data_publi',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $campanha = Campanha::create([
            'titulo'          => $request->titulo,
            'subtitulo'       => $request->subtitulo,
            'descricao'       => $request->descricao,
            'tipo_sangue'     => $request->tipo_sangue,
            'hemocentro_id'   => $request->hemocentro_id,
            'data_publi'      => $request->data_publi,
            'data_expiracao'  => $request->data_expiracao,
            'status'          => true,
            'criado_por'      => Auth::id(),
            'total_disparado' => 0,
            'total_aberto'    => 0,
        ]);

        Log::info('CAMPANHA CRIADA', [
            'campanha_id' => $campanha->id,
            'titulo'      => $campanha->titulo,
            'criado_por'  => Auth::id(),
            'timestamp'   => now(),
        ]);

        return response()->json([
            'message' => 'Campanha criada com sucesso!',
            'data'    => $campanha->load('hemocentro'),
        ], 201);
    }

    // PUT /api/auth/campanhas/{id}
    public function update(Request $request, $id)
    {
        $campanha = Campanha::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'titulo'         => 'sometimes|string|max:255',
            'subtitulo'      => 'nullable|string|max:255',
            'descricao'      => 'nullable|string',
            'tipo_sangue'    => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'data_publi'     => 'sometimes|date',
            'data_expiracao' => 'nullable|date',
            'status'         => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $campanha->update($request->only([
            'titulo', 'subtitulo', 'descricao',
            'tipo_sangue', 'data_publi', 'data_expiracao', 'status',
        ]));

        return response()->json([
            'message' => 'Campanha atualizada!',
            'data'    => $campanha->fresh('hemocentro'),
        ]);
    }

    // DELETE /api/auth/campanhas/{id}
    public function destroy($id)
    {
        $campanha = Campanha::findOrFail($id);
        $campanha->status = false;
        $campanha->save();
        $campanha->delete();

        return response()->json(['message' => 'Campanha removida!']);
    }

    // POST /api/auth/campanhas/{id}/disparar
    public function disparar($id)
    {
        $campanha = Campanha::findOrFail($id);

        $query = User::where('role_id', 1)
            ->where('status', 1)
            ->whereNotNull('email');

        if ($campanha->tipo_sangue) {
            $query->where('tipo_sang', $campanha->tipo_sangue);
        }

        if ($campanha->hemocentro_id) {
            $query->whereHas('triagens', function ($q) use ($campanha) {
                $q->where('hemocentro_id', $campanha->hemocentro_id);
            });
        }

        $doadores = $query->get(['id', 'name', 'email', 'tipo_sang', 'tempo_restricao', 'criado_em']);

        $doadoresSegmentados = $this->segmentarViaMl($doadores, $campanha);

        $totalDisparado = 0;
        foreach ($doadoresSegmentados as $doador) {
            try {
                Mail::raw(
                    "OlÃ¡, {$doador->name}!\n\n{$campanha->descricao}\n\nEquipe DoaVida",
                    function ($message) use ($doador, $campanha) {
                        $message->to($doador->email)
                                ->subject($campanha->titulo);
                    }
                );
                $totalDisparado++;
            } catch (\Throwable $e) {
                Log::warning('FALHA AO ENVIAR EMAIL DA CAMPANHA', [
                    'doador_id'   => $doador->id,
                    'campanha_id' => $campanha->id,
                    'erro'        => $e->getMessage(),
                ]);
            }
        }

        $campanha->update(['total_disparado' => $totalDisparado]);

        Log::info('CAMPANHA DISPARADA', [
            'campanha_id'     => $campanha->id,
            'titulo'          => $campanha->titulo,
            'total_elegÃ­veis' => $doadores->count(),
            'total_disparado' => $totalDisparado,
            'disparado_por'   => Auth::id(),
            'timestamp'       => now(),
        ]);

        return response()->json([
            'message'         => "Campanha disparada para {$totalDisparado} doadores.",
            'total_elegiveis' => $doadores->count(),
            'total_disparado' => $totalDisparado,
            'segmentacao'     => $totalDisparado < $doadores->count() ? 'ml' : 'completa',
        ]);
    }

    private function segmentarViaMl($doadores, Campanha $campanha)
    {
        $mlUrl = config('services.ml.url');
        $mlKey = config('services.ml.key');

        if (!$mlUrl) {
            Log::info('ML nÃ£o configurado â€” disparando para todos os elegÃ­veis', [
                'campanha_id' => $campanha->id,
            ]);
            return $doadores;
        }

        try {
            $doacaoStats = Doacao::query()
                ->whereIn('user_id', $doadores->pluck('id'))
                ->selectRaw('user_id, COUNT(*) as frequencia_doacoes, COALESCE(SUM(quantidade), 0) as volume_total_cc, MAX(data_hora_doacao) as ultima_doacao, MIN(data_hora_doacao) as primeira_doacao')
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $payload = [
                'campanha_id' => $campanha->id,
                'tipo_sangue' => $campanha->tipo_sangue,
                'doadores'    => $doadores->map(function ($d) use ($doacaoStats) {
                    $stats = $doacaoStats->get($d->id);
                    $ultimaDoacao = $stats?->ultima_doacao ? \Carbon\Carbon::parse($stats->ultima_doacao) : null;
                    $primeiraReferencia = $stats?->primeira_doacao ?: $d->criado_em;
                    $primeiraDoacao = $primeiraReferencia ? \Carbon\Carbon::parse($primeiraReferencia) : now();
                    $recenciaMeses = $ultimaDoacao ? $ultimaDoacao->diffInMonths(now()) : $primeiraDoacao->diffInMonths(now());
                    $tempoMeses = max(1, $primeiraDoacao->diffInMonths(now()));
                    $frequencia = (int) ($stats?->frequencia_doacoes ?? 0);
                    $riscoInatividade = $recenciaMeses <= 6 ? 'Ativo' : ($recenciaMeses <= 12 ? 'Atencao' : ($recenciaMeses <= 24 ? 'Em_Risco' : 'Inativo'));

                    return [
                        'id'                          => $d->id,
                        'tipo_sang'                   => $d->tipo_sang,
                        'tempo_restricao'             => optional($d->tempo_restricao)->toDateString(),
                        'cadastrado_em'               => optional($d->criado_em)->toDateTimeString(),
                        'recencia_meses'              => $recenciaMeses,
                        'frequencia_doacoes'          => $frequencia,
                        'volume_total_cc'             => (float) ($stats?->volume_total_cc ?? 0),
                        'tempo_desde_primeira_doacao' => $tempoMeses,
                        'risco_inatividade'           => $riscoInatividade,
                    ];
                })->values()->all(),
            ];

            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => "Bearer {$mlKey}"])
                ->post("{$mlUrl}/segmentar", $payload);

            if ($response->successful()) {
                $ids = $response->json('user_ids', []);
                if (!empty($ids)) {
                    $segmentados = $doadores->whereIn('id', $ids)->values();
                    Log::info('ML SEGMENTOU DOADORES', [
                        'campanha_id'  => $campanha->id,
                        'total_antes'  => $doadores->count(),
                        'total_depois' => $segmentados->count(),
                    ]);
                    return $segmentados;
                }
            }

            Log::warning('ML retornou resposta invÃ¡lida â€” usando fallback', [
                'campanha_id' => $campanha->id,
                'status'      => $response->status(),
            ]);

        } catch (\Throwable $e) {
            Log::warning('ML indisponÃ­vel â€” usando fallback local', [
                'campanha_id' => $campanha->id,
                'erro'        => $e->getMessage(),
            ]);
        }

        return $doadores->filter(function ($doador) {
            if (!$doador->tempo_restricao) return true;
            return now()->gt($doador->tempo_restricao);
        })->values();
    }
}

