<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doe-Vida | Logs</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #111827;
            color: #e5e7eb;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            background: #7f1d1d;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        a {
            color: #fee2e2;
            text-decoration: none;
        }

        main {
            width: min(1180px, calc(100% - 24px));
            margin: 20px auto;
        }

        .meta {
            margin: 0 0 14px;
            color: #cbd5e1;
            word-break: break-all;
        }

        .viewer {
            min-height: 70vh;
            overflow: auto;
            border: 1px solid #374151;
            border-radius: 8px;
            background: #030712;
            padding: 14px;
        }

        .line {
            margin: 0;
            min-height: 20px;
            color: #d1d5db;
            font-family: Consolas, "Courier New", monospace;
            font-size: 13px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .line.error {
            color: #fecaca;
        }

        .line.warning {
            color: #fde68a;
        }

        .line.info {
            color: #bfdbfe;
        }
    </style>
</head>
<body>
    <header>
        <h1>Logs do Doe-Vida</h1>
        <a href="/hemocentros">Hemocentros</a>
    </header>

    <main>
        <p class="meta">Exibindo as ultimas 300 linhas de {{ $path }}</p>

        <section class="viewer" aria-label="Conteudo do arquivo de log">
            @forelse ($lines as $line)
                @php
                    $level = str_contains($line, '.ERROR')
                        ? 'error'
                        : (str_contains($line, '.WARNING') ? 'warning' : (str_contains($line, '.INFO') ? 'info' : ''));
                @endphp
                <p class="line {{ $level }}">{{ $line }}</p>
            @empty
                <p class="line">Nenhum log encontrado.</p>
            @endforelse
        </section>
    </main>
</body>
</html>
