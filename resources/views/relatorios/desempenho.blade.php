<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo ?? 'Relatório de Desempenho' }} — Doe Vida</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1F2937; background: #fff; }

        .header {
            background-color: #1D4ED8;
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
        .kpi.azul    { border-top: 3px solid #2563EB; }
        .kpi.verde   { border-top: 3px solid #059669; }
        .kpi.laranja { border-top: 3px solid #D97706; }
        .kpi.cinza   { border-top: 3px solid #6B7280; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #111827; }
        .kpi-label { font-size: 8px; color: #6B7280; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { margin: 0 20px 16px; }
        .section-title {
            font-size: 11px; font-weight: bold; color: #1E3A5F;
            text-transform: uppercase; letter-spacing: 0.6px;
            border-bottom: 2px solid #BFDBFE; padding-bottom: 4px; margin-bottom: 10px;
        }
        .chart-area { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px; padding: 12px; }

        .two-col { display: table; width: calc(100% - 40px); margin: 0 20px 16px; border-spacing: 12px; }
        .two-col-cell { display: table-cell; width: 50%; vertical-align: top; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead tr { background: #1E3A5F; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #EFF6FF; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #F3F4F6; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        .bar-row   { display: table; width: 100%; margin-bottom: 6px; }
        .bar-label { display: table-cell; font-size: 9px; color: #374151; vertical-align: middle; width: 140px; }
        .bar-track-cell { display: table-cell; vertical-align: middle; }
        .bar-track { background: #E5E7EB; border-radius: 3px; height: 13px; }
        .bar-fill  { height: 13px; border-radius: 3px; background: #2563EB; }
        .bar-count { display: table-cell; width: 50px; text-align: right; font-size: 9px; color: #6B7280; vertical-align: middle; }

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
            <h1>{{ $titulo ?? 'Relatório de Desempenho - ' . ($periodo_label ?? 'últimos 6 meses') }}</h1>
            <div class="subtitle">{{ $subtitulo ?? 'Performance operacional, volume coletado e produtividade.' }} Tipo: {{ $tipo_filtro ?? 'Todos os tipos' }}</div>
        </div>
        <div class="header-right">
            <strong>{{ $unidade }}</strong>
            Período: {{ $periodo_label ?? 'últimos 6 meses' }}<br>
            Tipo: {{ $tipo_filtro ?? 'Todos os tipos' }}<br>
            Gerado em {{ $gerado_em }}
        </div>
    </div>
</div>

{{-- KPIs --}}
<div class="kpi-wrap">
    <table>
        <tr>
            <td class="kpi azul">
                <div class="kpi-value">{{ number_format($total_doacoes, 0, ',', '.') }}</div>
                <div class="kpi-label">Doações no Período</div>
            </td>
            <td class="kpi verde">
                <div class="kpi-value">{{ number_format($volume_total_ml / 1000, 2, ',', '.') }} L</div>
                <div class="kpi-label">Volume Total</div>
            </td>
            <td class="kpi laranja">
                @php
                    $sinal = $variacao_pct > 0 ? '+' : '';
                @endphp
                <div class="kpi-value">
                    {{ $variacao_pct !== null ? $sinal . $variacao_pct . '%' : '—' }}
                </div>
                <div class="kpi-label">Variação vs Mês Anterior</div>
            </td>
            <td class="kpi cinza">
                <div class="kpi-value">{{ $hemocentros_ativos }}</div>
                <div class="kpi-label">Hemocentros Ativos</div>
            </td>
        </tr>
    </table>
</div>

{{-- Gráfico de Linha Mensal --}}
<div class="section">
    <div class="section-title">Evolução Mensal de Doações</div>
    <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Série mensal de doações e volume coletado no período {{ $periodo_label ?? 'últimos 6 meses' }}.</div>
    <div class="chart-area">
        @php
            $vals   = array_column($performance_mensal, 'total');
            $labels = array_column($performance_mensal, 'label');
            $maxVal = max($vals) ?: 1;
            $minVal = min($vals);
            $n      = count($vals);

            $vbW = 500; $vbH = 100; $padX = 30; $padY = 15;
            $plotW = $vbW - $padX * 2;
            $plotH = $vbH - $padY * 2;

            $points = [];
            $trendY = [];
            foreach ($vals as $i => $v) {
                $x = $padX + ($n > 1 ? $i * $plotW / ($n - 1) : $plotW / 2);
                $y = $padY + $plotH - ($maxVal > 0 ? $v / $maxVal * $plotH : 0);
                $points[] = "$x,$y";
                $trendY[] = $y;
            }
            $polyline = implode(' ', $points);

            // Linha de tendência (média constante)
            $avgY = array_sum($trendY) / count($trendY);
            $trendLine = "$padX,$avgY " . ($vbW - $padX) . ",$avgY";
        @endphp
        <svg viewBox="0 0 {{ $vbW }} {{ $vbH + 20 }}" width="100%" xmlns="http://www.w3.org/2000/svg">
            {{-- Grade --}}
            @for ($i = 0; $i <= 4; $i++)
            @php $gy = $padY + $i * $plotH / 4; @endphp
            <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $vbW - $padX }}" y2="{{ $gy }}"
                  stroke="#E5E7EB" stroke-width="0.5" stroke-dasharray="3,3"/>
            @endfor

            {{-- Linha de tendência --}}
            <polyline points="{{ $trendLine }}" fill="none" stroke="#D97706" stroke-width="1" stroke-dasharray="4,3" opacity="0.7"/>

            {{-- Linha de dados --}}
            <polyline points="{{ $polyline }}" fill="none" stroke="#2563EB" stroke-width="1.5"/>

            {{-- Pontos e valores --}}
            @foreach($vals as $i => $v)
            @php
                $x = $padX + ($n > 1 ? $i * $plotW / ($n - 1) : $plotW / 2);
                $y = $padY + $plotH - ($maxVal > 0 ? $v / $maxVal * $plotH : 0);
            @endphp
            <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#2563EB"/>
            @if($v > 0)
            <text x="{{ $x }}" y="{{ $y - 5 }}" font-size="8" fill="#1E3A5F" text-anchor="middle">{{ $v }}</text>
            @endif
            <text x="{{ $x }}" y="{{ $vbH + 16 }}" font-size="8" fill="#6B7280" text-anchor="middle">{{ $labels[$i] }}</text>
            @endforeach
        </svg>
        <div style="margin-top:4px; font-size:8px; color:#9CA3AF;">
            <span style="color:#2563EB; font-weight:bold;">&#9473;</span> Doações mensais
            &nbsp;&nbsp;
            <span style="color:#D97706;">- - -</span> Média do período
        </div>
    </div>
</div>

{{-- Desempenho por Unidade + Funcionários Mais Ativos --}}
<div class="two-col">
    <div class="two-col-cell">
        <div class="section-title">Desempenho por Unidade</div>
        <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Comparativo de doações, volume e participação de cada hemocentro.</div>
        @if($por_hemocentro->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Unidade</th>
                    <th class="right">Doações</th>
                    <th class="right">Volume (L)</th>
                    <th class="right">Média/mês</th>
                    <th class="right">% Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($por_hemocentro as $h)
                <tr>
                    <td>{{ $h['nome'] }}</td>
                    <td class="right">{{ number_format($h['total'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($h['volume_ml'] / 1000, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($h['media_mes'], 1, ',', '.') }}</td>
                    <td class="right">{{ $h['pct_total'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Sem dados</div>
        @endif
    </div>
    <div class="two-col-cell">
        <div class="section-title">Funcionários Mais Ativos</div>
        <div style="font-size:8px; color:#6B7280; margin-bottom:6px;">Ranking de funcionários com mais registros de doação no período selecionado.</div>
        @if($top_funcionarios->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Unidade</th>
                    <th class="right">Doações Reg.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_funcionarios as $i => $f)
                <tr>
                    <td>
                        @if($i === 0)<strong>@endif
                        {{ $f['name'] }}
                        @if($i === 0)</strong>@endif
                    </td>
                    <td style="color:#6B7280;">{{ $f['unidade'] }}</td>
                    <td class="right"><strong>{{ $f['total'] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Sem dados</div>
        @endif
        <div style="margin-top:10px; padding:8px; background:#EFF6FF; border-radius:4px; font-size:9px;">
            <strong>Taxa de Ocupação (mês atual):</strong> {{ $taxa_ocupacao }}%
            <div style="font-size:8px; color:#6B7280; margin-top:2px;">Agendamentos confirmados / total do mês</div>
        </div>
    </div>
</div>

<div class="footer">
    <span class="footer-l">Doe Vida &copy; {{ date('Y') }} — Documento Gerado Automaticamente</span>
    <span class="footer-c">Relatório Confidencial — Uso Interno</span>
    <span class="footer-r">{{ $gerado_em }}</span>
</div>

</body>
</html>
