<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Doações</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #B91C1C; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #B91C1C; margin: 0; font-size: 24px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1E3A5F; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #E5E7EB; }
        tr:nth-child(even) { background: #F9FAFB; }
        .footer { margin-top: 30px; font-size: 10px; color: #9CA3AF; text-align: center; border-top: 1px solid #E5E7EB; padding-top: 10px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🩸 Doe Vida — Relatório de Doações</h1>
        <p>Sistema de Gestão de Doação de Sangue</p>
    </div>

    <div class="info">
        <p><strong>Unidade:</strong> {{ $unidade }}</p>
        <p><strong>Período:</strong> Últimos {{ $periodo }} dias</p>
        <p><strong>Gerado em:</strong> {{ $gerado_em }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Doador</th>
                <th>Tipo</th>
                <th class="text-right">Quantidade</th>
                <th>Funcionário</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doacoes as $d)
            <tr>
                <td>{{ \Carbon\Carbon::parse($d->data_hora_doacao)->format('d/m/Y H:i') }}</td>
                <td>{{ $d->doador?->name ?? 'Não identificado' }}</td>
                <td>{{ $d->tipo_sangue }}</td>
                <td class="text-right">{{ number_format($d->quantidade, 0, ',', '.') }} mL</td>
                <td>{{ $d->funcionario?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Doe Vida &copy; {{ date('Y') }} · Documento Gerado Automaticamente
    </div>
</body>
</html>
