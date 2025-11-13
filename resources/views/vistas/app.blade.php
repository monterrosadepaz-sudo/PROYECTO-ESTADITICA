<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadística | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f6f7f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            background-color: #3B3B63;
            color: #fff;
            min-height: 100vh;
        }
        .sidebar a {
            color: #d3b1c0;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #fff;
        }
        .header {
            background-color: #3B73B9;
            color: #fff;
            padding: 1rem;
        }
        .card-stat {
            border-left: 5px solid #00B6CB;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h4 class="mb-4">Estadística</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="{{ route('sesion.index') }}" class="nav-link">Sesiones</a></li>

                </ul>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-10">
                <div class="header">
                    <h5>@yield('header')</h5>
                </div>
                <div class="p-4">
                    @yield('contenido')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
