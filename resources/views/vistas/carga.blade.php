<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema-Estadística</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            background-color: #1e1e2f;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .bar-container {
            width: 200px;
            height: 10px;
            background-color: #444;
            border-radius: 5px;
            overflow: hidden;
        }

        .bar {
            height: 100%;
            background-color: #00b6cb;
            animation: load 3s linear forwards;
        }

        @keyframes load {
            from { width: 0%; }
            to { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="logo">Sistema-Estadistico</div>
    <div class="bar-container">
        <div class="bar"></div>
    </div>

    <script>
        setTimeout(() => {
            window.location.href = "{{ route('dashboard') }}";
        }, 3000);
    </script>
</body>
</html>
