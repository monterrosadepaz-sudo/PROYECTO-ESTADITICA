<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte estadístico</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; background-color: #1c1c1c; color: #f0f0f0; padding: 20px; }
        h1, h2, h3, h4 { text-align: center; color: #00bfff; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 5px; }
        .btn { display: inline-block; margin: 10px auto; padding: 8px 16px; background-color: #00bfff; color: white; text-decoration: none; border-radius: 4px; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #555; padding: 8px; text-align: center; }
        .table th { background-color: #333; }
        canvas { display: block; margin: 30px auto; }
    </style>
</head>
<body>
    <h1>Reporte estadístico</h1>

    @unless(request()->routeIs('sesion.reporte'))
        <a href="{{ route('sesion.reporte', $sesion->id) }}" class="btn">Descargar PDF</a>
    @endunless

    <h2>Sesión: {{ $sesion->nombre_clave }} ({{ $sesion->tipo_serie }})</h2>

    @if($sesion->tipo_serie === 'simple')
        <h3>Valores registrados</h3>
        <ul>
            @foreach($valores as $v)
                <li>{{ number_format($v, 4) }}</li>
            @endforeach
        </ul>
    @elseif($sesion->tipo_serie === 'agrupada')
        <h3>Clases agrupadas</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Límite inferior</th>
                    <th>Límite superior</th>
                    <th>Frecuencia</th>
                    <th>PM</th>
                    <th>PMF</th>
                    <th>Frecuencia relativa</th>
                    <th>Frecuencia acumulada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clases as $c)
                    <tr>
                        <td>{{ $c['lim_inf'] }}</td>
                        <td>{{ $c['lim_sup'] }}</td>
                        <td>{{ $c['frecuencia'] }}</td>
                        <td>{{ number_format($c['marca'], 2) }}</td>
                        <td>{{ number_format($c['pmf'], 2) }}</td>
                        <td>{{ number_format($c['frecuencia_relativa'] * 100, 2) }}%</td>
                        <td>{{ $c['frecuencia_acumulada'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($estadisticas)
        <h3>Resultados estadísticos</h3>
        <ul>
            <li><strong>Media Aritmetica:</strong> {{ $estadisticas['media'] }}</li>
            <li><strong>Mediana:</strong> {{ $estadisticas['mediana'] }}</li>
            <li><strong>Moda:</strong> 
                {{ is_array($estadisticas['moda']) ? implode(', ', $estadisticas['moda']) : $estadisticas['moda'] }}
            </li>
            <li><strong>Varianza:</strong> {{ $estadisticas['varianza'] }}</li>
            <li><strong>Desviación estándar:</strong> {{ $estadisticas['desviacion'] }}</li>
        </ul>
    @else
        <p>No hay datos suficientes para calcular estadísticas.</p>
    @endif

    @unless(request()->routeIs('sesion.reporte'))
        <canvas id="grafico" width="600" height="300"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const tipo = "{{ $sesion->tipo_serie }}";

            let labels = [];
            let data = [];

            @if($sesion->tipo_serie === 'simple')
                labels = @json(range(1, count($valores)));
                data = @json($valores);
            @elseif($sesion->tipo_serie === 'agrupada')
                labels = @json(array_map(fn($c) => $c['lim_inf'] . '-' . $c['lim_sup'], $clases));
                data = @json(array_column($clases, 'frecuencia'));
            @endif

            new Chart(document.getElementById('grafico'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: tipo === 'simple' ? 'Valores individuales' : 'Frecuencia por clase',
                        data: data,
                        backgroundColor: 'rgba(0, 191, 255, 0.6)',
                        borderColor: 'rgba(0, 191, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>
    @endunless
</body>
</html>

