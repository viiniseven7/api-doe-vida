<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Hello World Laravel</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; }
        .card { background: white; padding: 2rem; border-radius: 10px; shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #e3342f; } /* Vermelho sangue para o Doe-Vida */
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $texto }}</h1>
        <p>O seu ambiente Laravel está configurado com sucesso!</p>
    </div>
</body>
</html>