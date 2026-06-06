<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo ?? 'Relatório de Triagens' }} — Doe Vida</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1F2937; background: #fff; }

        .header {
            background-color: #6D28D9;
            color: white; padding: 18px 24px; margin-bottom: 16px;
        }
        .header-inner { display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 9px; opacity: 0.9; }
        .header h1 { font-size: 20px; font-weight: bold; letter-spacing: 0.5px; }
        .header .subtitle { font-size: 10px; opacity: 0.85; margin-top: 2px; }
        .header-right strong { font-size: 11px; display: block; margin-bottom: 2px; }

        .kpi-wrap { margin: 0 20px 16px; }
        .kpi-wrap table { width: 100%; border-collapse: separate; border-spacing: 8px; }
        .kpi { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px; padding: 10px 12px; }
        .kpi.roxo   { border-top: 3px solid #7C3AED; }
        .kpi.verde  { border-top: 3px solid #059669; }
        .kpi.vermelho { border-top: 3px solid #B91C1C; }
        .kpi.cinza  { border-top: 3px solid #6B7280; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #111827; }
        .kpi-label { font-size: 8px; color: #6B7280; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { margin: 0 20px 16px; }
        .section-title {
            font-size: 11px; font-weight: bold; color: #4C1D95;
            text-transform: uppercase; letter-spacing: 0.6px;
            border-bottom: 2px solid #DDD6FE; padding-bottom: 4px; margin-bottom: 10px;
        }
        .chart-area { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px; padding: 12px; }

        .motivo-row { display: table; width: 100%; margin-bottom: 6px; }
        .motivo-label { display: table-cell; font-size: 9px; color: #374151; vertical-align: middle; width: 160px; }
        .motivo-track { display: table-cell; vertical-align: middle; }
        .motivo-bar-bg { background: #E5E7EB; border-radius: 3px; height: 12px; }
        .motivo-bar-fill { height: 12px; border-radius: 3px; background: #7C3AED; }
        .motivo-count { display: table-cell; width: 30px; text-align: right; font-size: 9px; color: #6B7280; vertical-align: middle; }

        .two-col { display: table; width: calc(100% - 40px); margin: 0 20px 16px; border-spacing: 12px; }
        .two-col-cell { display: table-cell; width: 50%; vertical-align: top; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background: #4C1D95; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px; }
        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }
        tbody tr:nth-child(even) { background: #F5F3FF; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #F3F4F6; }
        tbody td.center { text-align: center; }
        tbody td.right  { text-align: right; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 9px; font-size: 8px; font-weight: bold; }
        .badge-apto   { background: #D1FAE5; color: #065F46; }
        .badge-inapto { background: #FEE2E2; color: #B91C1C; }

        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #F9FAFB; border-top: 1px solid #E5E7EB;
            padding: 5px 20px; display: table; width: 100%;
            font-size: 8px; color: #9CA3AF;
        }
        .footer-l { display: table-cell; }
        .footer-c { display: table-cell; text-align: center; }
        .footer-r { display: table-cell; text-align: right; }

        .empty { text-align: center; padding: 30px; color: #9CA3AF; font-style: italic; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <h1>{{ $titulo ?? 'Relatório de Triagens - ' . ($periodo_label ?? ('últimos ' . $periodo . ' dias')) }}</h1>
            <div class="subtitle">{{ $subtitulo ?? 'Aptidão, motivos de inaptidão e triagens realizadas.' }} Tipo: {{ $tipo_filtro ?? 'Todos os tipos' }}</div>
        </div>
        <div class="header-right">
            <strong>{{ $unidade }}</strong>
            Período: {{ $periodo_label ?? ('últimos ' . $periodo . ' dias') }}<br>
            Tipo: {{ $tipo_filtro ?? 'Todos os tipos' }}<br>
            Gerado em {{ $gerado_em }}
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-wrap">
    <table>
        <tr>
            <td class="kpi roxo">
                <div class="kpi-value">{{ number_format($total, 0, ',', '.') }}</div>
                <div class="kpi-label">Total de Triagens</div>
            </td>
            <td class="kpi verde">
                <div class="kpi-value">{{ number_format($aptas, 0, ',', '.') }}</div>
                <div class="kpi-label">Aptas</div>
            </td>
            <td class="kpi vermelho">
                <div class="kpi-value">{{ number_format($inaptas, 0, ',', '.') }}</div>
                <div class="kpi-label">Inaptas</div>
            </td>
            <td class="kpi cinza">
                <div class="kpi-value">{{ $taxa_aptidao }}%</div>
                <div class="kpi-label">Taxa de Aptidão</div>
                @if($media_pressao)
                <div style="font-size:8px; color:#9CA3AF; margin-top:2px;">PA sistólica (aptos): {{ $media_pressao }} mmHg</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- Motivos de Inaptidão + Por Hemocentro --}}
<div class="two-col">
    <div class="two-col-cell">
        <div class="section-title">Principais Motivos de Inaptidão</div>
        <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Motivos mais frequentes entre as triagens não aptas no período selecionado.</div>
        <div class="chart-area">
            @forelse($motivos as $motivo => $count)
            @php $pct = $max_motivo > 0 ? round($count / $max_motivo * 100) : 0; @endphp
            <div class="motivo-row">
                <div class="motivo-label">{{ Str::limit($motivo, 25) }}</div>
                <div class="motivo-track">
                    <div class="motivo-bar-bg"><div class="motivo-bar-fill" style="width:{{ $pct }}%;"></div></div>
                </div>
                <div class="motivo-count">{{ $count }}</div>
            </div>
            @empty
            <div class="empty">Nenhum motivo registrado</div>
            @endforelse
        </div>
    </div>
    <div class="two-col-cell">
        <div class="section-title">Por Unidade de Saúde</div>
        <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Quantidade de triagens registradas por hemocentro.</div>
        <div class="chart-area">
            @php $maxHemo = $por_hemocentro->max('total') ?: 1; @endphp
            @forelse($por_hemocentro as $h)
            @php $pct = round($h['total'] / $maxHemo * 100); @endphp
            <div class="motivo-row">
                <div class="motivo-label">{{ $h['nome'] }}</div>
                <div class="motivo-track">
                    <div class="motivo-bar-bg"><div class="motivo-bar-fill" style="width:{{ $pct }}%;"></div></div>
                </div>
                <div class="motivo-count">{{ $h['total'] }}</div>
            </div>
            @empty
            <div class="empty">Sem dados</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Tabela detalhada --}}
<div class="section">
    <div class="section-title">Registro Detalhado de Triagens</div>
    <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Lista das triagens exportadas com doador, funcionário, unidade e resultado.</div>
    @if($triagens->count() > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Doador</th>
                <th>Funcionário</th>
                <th>Unidade</th>
                <th class="center">Apto</th>
                <th>Motivo (se inapto)</th>
                <th class="right">PA Sist.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($triagens as $i => $t)
            <tr>
                <td class="center" style="color:#9CA3AF;">{{ $i + 1 }}</td>
                <td>{{ $t->data_triagem ? \Carbon\Carbon::parse($t->data_triagem)->format('d/m/Y') : '—' }}</td>
                <td>{{ $t->doador?->name ?? '—' }}</td>
                <td>{{ $t->funcionario?->name ?? '—' }}</td>
                <td>{{ $t->hemocentro?->nome ?? '—' }}</td>
                <td class="center">
                    <span class="badge {{ $t->apto ? 'badge-apto' : 'badge-inapto' }}">
                        {{ $t->apto ? 'Apto' : 'Inapto' }}
                    </span>
                </td>
                <td style="font-size:8px; color:#6B7280;">
                    {{ !$t->apto && $t->motivo_inaptidao ? \Illuminate\Support\Str::limit($t->motivo_inaptidao, 35) : '—' }}
                </td>
                <td class="right">
                    {{ $t->sinaisVitais?->pressao_sistolica ? $t->sinaisVitais->pressao_sistolica . '/' . $t->sinaisVitais->pressao_diastolica : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:6px; text-align:right; font-size:9px; color:#6B7280;">
        <strong>Total:</strong> {{ $triagens->count() }}
        &nbsp;|&nbsp;
        <strong>Aptas:</strong> {{ $aptas }}
        &nbsp;|&nbsp;
        <strong>Inaptas:</strong> {{ $inaptas }}
    </div>
    @else
    <div class="empty">Nenhuma triagem registrada no período selecionado.</div>
    @endif
</div>

<div class="footer">
    <span class="footer-l">Doe Vida &copy; {{ date('Y') }} — Documento Gerado Automaticamente</span>
    <span class="footer-c">Relatório Confidencial — Uso Interno</span>
    <span class="footer-r">{{ $gerado_em }}</span>
</div>

</body>
</html>
