<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Agendamentos — Doe Vida</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1F2937; background: #fff; }

        .header {
            background: linear-gradient(135deg, #7F1D1D 0%, #B91C1C 50%, #DC2626 100%);
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
        .kpi {
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 6px; padding: 10px 12px;
        }
        .kpi.vermelho { border-top: 3px solid #B91C1C; }
        .kpi.verde    { border-top: 3px solid #059669; }
        .kpi.azul     { border-top: 3px solid #2563EB; }
        .kpi.laranja  { border-top: 3px solid #D97706; }
        .kpi.cinza    { border-top: 3px solid #6B7280; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #111827; }
        .kpi-label { font-size: 8px; color: #6B7280; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { margin: 0 20px 16px; }
        .section-title {
            font-size: 11px; font-weight: bold; color: #7F1D1D;
            text-transform: uppercase; letter-spacing: 0.6px;
            border-bottom: 2px solid #FECACA; padding-bottom: 4px; margin-bottom: 10px;
        }
        .chart-area { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px; padding: 12px; }

        .bar-row { display: table; width: 100%; margin-bottom: 6px; }
        .bar-cell-label { display: table-cell; width: 140px; font-size: 9px; color: #374151; vertical-align: middle; }
        .bar-cell-track { display: table-cell; vertical-align: middle; }
        .bar-track { background: #E5E7EB; border-radius: 3px; height: 13px; }
        .bar-fill  { height: 13px; border-radius: 3px; background: #B91C1C; }
        .bar-cell-count { display: table-cell; width: 40px; text-align: right; font-size: 9px; color: #6B7280; vertical-align: middle; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background: #7F1D1D; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #FEF2F2; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #F3F4F6; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 9px; font-size: 8px; font-weight: bold; }
        .badge-age { background: #DBEAFE; color: #1D4ED8; }
        .badge-con { background: #FEF3C7; color: #92400E; }
        .badge-fin { background: #D1FAE5; color: #065F46; }
        .badge-can { background: #FEE2E2; color: #B91C1C; }
        .badge-exc { background: #F3F4F6; color: #6B7280; }

        .two-col { display: table; width: calc(100% - 40px); margin: 0 20px 16px; border-spacing: 12px; }
        .two-col-cell { display: table-cell; width: 50%; vertical-align: top; }

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
            <h1>Relatório de Agendamentos</h1>
            <div class="subtitle">Doe Vida — Sistema de Gestão de Doações de Sangue</div>
        </div>
        <div class="header-right">
            <strong>{{ $unidade }}</strong>
            Período: últimos {{ $periodo }} dias<br>
            Gerado em {{ $gerado_em }}
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-wrap">
    <table>
        <tr>
            <td class="kpi cinza">
                <div class="kpi-value">{{ number_format($total, 0, ',', '.') }}</div>
                <div class="kpi-label">Total</div>
            </td>
            <td class="kpi verde">
                <div class="kpi-value">{{ number_format($por_status['FIN'], 0, ',', '.') }}</div>
                <div class="kpi-label">Concluídos (FIN)</div>
            </td>
            <td class="kpi vermelho">
                <div class="kpi-value">{{ number_format($por_status['CAN'], 0, ',', '.') }}</div>
                <div class="kpi-label">Cancelados (CAN)</div>
            </td>
            <td class="kpi laranja">
                <div class="kpi-value">{{ number_format($por_status['CON'], 0, ',', '.') }}</div>
                <div class="kpi-label">Confirmados (CON)</div>
            </td>
            <td class="kpi azul">
                <div class="kpi-value">{{ $taxa_conclusao }}%</div>
                <div class="kpi-label">Taxa de Conclusão</div>
            </td>
        </tr>
    </table>
</div>

{{-- Gráficos: por hemocentro + por dia da semana --}}
<div class="two-col">
    <div class="two-col-cell">
        <div class="section-title">Agendamentos por Unidade</div>
        <div class="chart-area">
            @forelse($por_hemocentro as $h)
            @php $pct = $max_hemo > 0 ? round($h['total'] / $max_hemo * 100) : 0; @endphp
            <div class="bar-row">
                <div class="bar-cell-label">{{ $h['nome'] }}</div>
                <div class="bar-cell-track">
                    <div class="bar-track"><div class="bar-fill" style="width:{{ $pct }}%;"></div></div>
                </div>
                <div class="bar-cell-count">{{ $h['total'] }}</div>
            </div>
            @empty
            <div class="empty">Sem dados</div>
            @endforelse
        </div>
    </div>
    <div class="two-col-cell">
        <div class="section-title">Agendamentos por Dia da Semana</div>
        <div class="chart-area">
            @php
                $maxDia = max($por_dia_semana) ?: 1;
                $svgW = 260; $svgH = 70; $pad = 8;
                $diasKeys = array_keys($por_dia_semana);
                $diasVals = array_values($por_dia_semana);
                $n    = count($diasKeys);
                $barW = $n > 0 ? floor(($svgW - $pad * 2) / $n) - 2 : 20;
            @endphp
            <svg width="{{ $svgW }}" height="{{ $svgH + 20 }}" xmlns="http://www.w3.org/2000/svg">
                @foreach($diasVals as $idx => $val)
                @php
                    $barH = $maxDia > 0 ? (int)(($val / $maxDia) * ($svgH - $pad * 2)) : 0;
                    $x    = $pad + $idx * (($svgW - $pad * 2) / $n);
                    $y    = $svgH - $pad - $barH;
                @endphp
                <rect x="{{ $x + 1 }}" y="{{ $y }}" width="{{ max(1, $barW) }}" height="{{ max(0, $barH) }}"
                      fill="{{ $val === max($diasVals) ? '#991B1B' : '#B91C1C' }}" rx="2"/>
                @if($val > 0)
                <text x="{{ $x + $barW / 2 + 1 }}" y="{{ $y - 2 }}" font-size="7" fill="#374151" text-anchor="middle">{{ $val }}</text>
                @endif
                <text x="{{ $x + $barW / 2 + 1 }}" y="{{ $svgH + 14 }}" font-size="8" fill="#6B7280" text-anchor="middle">{{ $diasKeys[$idx] }}</text>
                @endforeach
            </svg>
        </div>
    </div>
</div>

{{-- Tabela detalhada --}}
<div class="section">
    <div class="section-title">Registro Detalhado de Agendamentos</div>
    @if($agendamentos->count() > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data / Hora</th>
                <th>Doador</th>
                <th>Unidade</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agendamentos as $i => $ag)
            @php
                $badgeMap = [
                    'AGE' => ['badge-age', 'Agendado'],
                    'CON' => ['badge-con', 'Confirmado'],
                    'FIN' => ['badge-fin', 'Concluído'],
                    'CAN' => ['badge-can', 'Cancelado'],
                ];
                [$badgeCls, $badgeTxt] = $badgeMap[$ag->status_agendamento] ?? ['badge-exc', $ag->status_agendamento];
            @endphp
            <tr>
                <td class="center" style="color:#9CA3AF;">{{ $i + 1 }}</td>
                <td>{{ $ag->data_hora_doacao ? \Carbon\Carbon::parse($ag->data_hora_doacao)->format('d/m/Y H:i') : '—' }}</td>
                <td>{{ $ag->doador?->name ?? '—' }}</td>
                <td>{{ $ag->hemocentro?->nome ?? '—' }}</td>
                <td class="center"><span class="badge {{ $badgeCls }}">{{ $badgeTxt }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:6px; text-align:right; font-size:9px; color:#6B7280;">
        <strong>Total de Registros:</strong> {{ $agendamentos->count() }}
    </div>
    @else
    <div class="empty">Nenhum agendamento registrado no período selecionado.</div>
    @endif
</div>

<div class="footer">
    <span class="footer-l">Doe Vida &copy; {{ date('Y') }} — Documento Gerado Automaticamente</span>
    <span class="footer-c">Relatório Confidencial — Uso Interno</span>
    <span class="footer-r">{{ $gerado_em }}</span>
</div>

</body>
</html>
