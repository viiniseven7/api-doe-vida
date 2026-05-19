<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doe-Vida | Hemocentros</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f8fb;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            background: #b91c1c;
            color: #fff;
            padding: 28px 24px;
        }

        main {
            width: min(1120px, calc(100% - 32px));
            margin: 28px auto;
        }

        h1 {
            margin: 0;
            font-size: 30px;
        }

        .subtitle {
            margin: 8px 0 0;
            color: #fee2e2;
        }

        .summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .summary strong {
            color: #991b1b;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .card {
            min-height: 190px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 8px 18px rgba(31, 41, 55, 0.06);
        }

        .card h2 {
            margin: 0 0 10px;
            color: #991b1b;
            font-size: 20px;
            line-height: 1.25;
        }

        .status {
            display: inline-block;
            margin-bottom: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info {
            margin: 7px 0;
            line-height: 1.45;
        }

        .empty {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
        }

        @media (max-width: 640px) {
            header {
                padding: 24px 16px;
            }

            h1 {
                font-size: 24px;
            }

            .summary {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Doe-Vida</h1>
        <p class="subtitle">Hemocentros disponiveis para atendimento e agendamento.</p>
    </header>

    <main>
        <div class="summary">
            <p><strong>{{ $hemocentros->count() }}</strong> hemocentros ativos cadastrados</p>
            <p>Dados carregados direto do banco.</p>
        </div>

        @if ($hemocentros->isEmpty())
            <section class="empty">
                Nenhum hemocentro ativo cadastrado.
            </section>
        @else
            <section class="grid" aria-label="Lista de hemocentros">
                @foreach ($hemocentros as $hemocentro)
                    <article class="card">
                        <h2>{{ $hemocentro->nome }}</h2>
                        <span class="status">{{ $hemocentro->status_agendamento }}</span>
                        <p class="info"><strong>Cidade:</strong> {{ $hemocentro->cidade }} - {{ $hemocentro->uf }}</p>
                        <p class="info"><strong>Endereco:</strong> {{ $hemocentro->endereco }}, {{ $hemocentro->numero }}</p>
                        <p class="info"><strong>Bairro:</strong> {{ $hemocentro->bairro }}</p>
                        <p class="info"><strong>Telefone:</strong> {{ $hemocentro->telefone }}</p>
                        <p class="info"><strong>Email:</strong> {{ $hemocentro->email }}</p>
                    </article>
                @endforeach
            </section>
        @endif
    </main>
</body>
</html>
