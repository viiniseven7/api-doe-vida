<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Doadores</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🩸 Doe Vida — Relatório de Doadores</h1>
        <p>Sistema de Gestão de Doação de Sangue</p>
    </div>

    <div class="info">
        <p><strong>Unidade de Referência:</strong> {{ $unidade }}</p>
        <p><strong>Total de Doadores:</strong> {{ count($doadores) }}</p>
        <p><strong>Gerado em:</strong> {{ $gerado_em }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>E-mail</th>
                <th>Tipo Sanguíneo</th>
                <th>Telefone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doadores as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->cpf }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->tipo_sang ?? '-' }}</td>
                <td>{{ $u->telefone ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Doe Vida &copy; {{ date('Y') }} · Documento Gerado Automaticamente
    </div>
</body>
</html>
