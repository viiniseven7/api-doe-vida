<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado de Doação</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 0; }
        .certificate-container {
            width: 800px;
            height: 550px;
            padding: 50px;
            border: 20px solid #B91C1C;
            margin: 20px auto;
            position: relative;
            background-color: #fff;
            text-align: center;
        }
        .inner-border {
            border: 2px solid #1E3A5F;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
        }
        .header h1 { color: #B91C1C; font-size: 48px; margin: 0; text-transform: uppercase; }
        .header h2 { color: #1E3A5F; font-size: 24px; margin: 10px 0 40px 0; }
        .content { font-size: 20px; line-height: 1.6; margin-bottom: 50px; }
        .content b { color: #B91C1C; }
        .signature-section { margin-top: 60px; }
        .signature {
            display: inline-block;
            width: 250px;
            border-top: 1px solid #333;
            margin: 0 20px;
            padding-top: 10px;
            font-size: 14px;
        }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            font-size: 12px;
            color: #9CA3AF;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 150px;
            color: rgba(185, 28, 28, 0.05);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="inner-border">
            <div class="watermark">🩸</div>
            
            <div class="header">
                <h1>Certificado</h1>
                <h2>de Doação de Sangue</h2>
            </div>

            <div class="content">
                Certificamos para os devidos fins que o(a) Sr(a).<br>
                <b style="font-size: 28px;">{{ $doacao->doador->name }}</b><br>
                portador(a) do CPF <b>{{ $doacao->doador->cpf }}</b>, realizou com sucesso<br>
                uma doação de sangue no dia <b>{{ \Carbon\Carbon::parse($doacao->data_hora_doacao)->format('d/m/Y') }}</b><br>
                na unidade <b>{{ $doacao->hemocentro->nome }}</b>.
            </div>

            <p style="font-size: 16px; margin-top: 40px;">
                Seu gesto salva vidas. Agradecemos imensamente por sua solidariedade.
            </p>

            <div class="signature-section">
                <div class="signature">
                    <b>DOE VIDA</b><br>
                    Sistema de Gestão
                </div>
                <div class="signature">
                    <b>{{ $doacao->hemocentro->nome }}</b><br>
                    Unidade de Coleta
                </div>
            </div>

            <div class="footer">
                Este certificado é um documento oficial gerado pelo sistema DoaVida em {{ date('d/m/Y H:i') }}.<br>
                Código de Autenticação: {{ strtoupper(substr(md5($doacao->id . 'doavida'), 0, 12)) }}
            </div>
        </div>
    </div>
</body>
</html>