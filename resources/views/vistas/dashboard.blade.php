@extends('vistas.app')

@section('contenido')
<div class="container mt-5 text-white">
    <h1 style="color: gray" class="mb-4">Bienvenido al Dashboard</h1>
    <p class="text-muted">Define el nombre de la sesión y el tipo de datos que vas a ingresar.</p>

    {{-- Sesión activa --}}
    @if(session('sesion_id'))
        <div class="alert alert-info">
            Sesión activa: <strong>{{ session('sesion_id') }}</strong> ({{ session('tipo_serie') }})
            <form method="POST" action="{{ route('sesion.cerrar') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">Cerrar sesión</button>
            </form>
        </div>
    @endif

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario de inicio de sesión --}}
    @if(!session('sesion_id'))
        <div class="card bg-dark mb-4">
            <div class="card-header">Iniciar nueva sesión</div>
            <div class="card-body">
                <form action="{{ route('sesion.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre_clave" class="form-label">Nombre clave</label>
                        <input type="text" name="nombre_clave" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_serie" class="form-label">Tipo de serie</label>
                        <select name="tipo_serie" class="form-select" required>
                            <option value="simple">Simple</option>
                            <option value="agrupada">Agrupada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </form>
            </div>
        </div>
    @endif

    {{-- Formulario de ingreso de datos --}}
    @if(session('sesion_id'))
        <div class="card bg-dark mb-4">
            <div class="card-header">Ingreso de datos para sesión activa</div>
            <div class="card-body">
                {{-- Serie simple --}}
                @if(session('tipo_serie') === 'simple')
                    <div class="mb-3">
                        <label for="embudo" class="form-label">Modo embudo: ingresa un valor y presiona Enter</label>
                        <input type="number" id="embudo" class="form-control" placeholder="Ej: 7.5" autofocus>
                    </div>

                    <div class="mb-3">
                        <label>Valores ingresados:</label>
                        <ul id="serie-visual" class="list-group mb-2"></ul>
                        <p id="contador" class="text-muted">Cantidad: 0</p>
                        <button type="button" class="btn btn-danger" onclick="resetSerie()">Reiniciar serie</button>
                    </div>

                    <form method="POST" action="{{ route('datos.simples.store') }}" onsubmit="return prepararEnvio()">
                        @csrf
                        <input type="hidden" name="valores" id="valores-hidden">
                        <button type="submit" class="btn btn-success">Guardar serie</button>
                    </form>

                {{-- Serie agrupada con embudo --}}
                @elseif(session('tipo_serie') === 'agrupada')
                    <div class="mb-3">
                        <label for="lim_inf" class="form-label">Límite inferior</label>
                        <input type="number" id="lim_inf" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="lim_sup" class="form-label">Límite superior</label>
                        <input type="number" id="lim_sup" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="frecuencia" class="form-label">Frecuencia</label>
                        <input type="number" id="frecuencia" class="form-control">
                    </div>
                    <button type="button" class="btn btn-info" onclick="agregarClase()">Agregar clase</button>

                    <h4 class="mt-4">Clases ingresadas</h4>
                    <table class="table table-bordered text-white" id="tabla-clases">
                        <thead>
                            <tr>
                                <th>Límite inferior</th>
                                <th>Límite superior</th>
                                <th>Frecuencia</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <form method="POST" action="{{ route('clases.agrupadas.store') }}" onsubmit="return prepararEnvioAgrupada()">
                        @csrf
                        <input type="hidden" name="clases_json" id="clases-json">
                        <input type="hidden" name="sesion_id" value="{{ session('sesion_id') }}">
                        <button type="submit" class="btn btn-success">Guardar serie agrupada</button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Sesiones registradas --}}
    <div class="card bg-dark">
        <div class="card-header">Sesiones registradas</div>
        <div class="card-body">
            @if(isset($sesiones) && $sesiones->count())
                @foreach($sesiones as $sesion)
                    <div class="mb-3 border-bottom pb-2">
                        <strong>{{ $sesion->nombre_clave }}</strong> ({{ $sesion->tipo_serie }})
                        <a href="{{ route('sesion.show', $sesion->id) }}" class="btn btn-sm btn-outline-info ms-2">Ver</a>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No hay sesiones registradas aún.</p>
            @endif
        </div>
    </div>

    {{-- Valores históricos --}}
    @if(isset($valores_hist) && count($valores_hist))
        <div class="card bg-dark mt-4">
            <div class="card-header">Valores históricos registrados</div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($valores_hist as $v)
                        <li class="list-group-item">{{ $v->valor }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
<script>
    // 🔹 Embudo para serie simple
    let serie = [];
    const input = document.getElementById('embudo');
    const lista = document.getElementById('serie-visual');
    const contador = document.getElementById('contador');

    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const valor = parseFloat(this.value);
                if (!isNaN(valor)) {
                    serie.push(valor);
                    actualizarVisual();
                    this.value = '';
                } else {
                    alert('Valor inválido. Se reiniciará la serie.');
                    resetSerie();
                }
            }
        });
    }

    function actualizarVisual() {
        lista.innerHTML = '';
        serie.forEach((v, i) => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = `#${i + 1}: ${v}`;
            lista.appendChild(li);
        });
        contador.textContent = `Cantidad: ${serie.length}`;
    }

    function resetSerie() {
        serie = [];
        actualizarVisual();
        input.value = '';
    }

    function prepararEnvio() {
        if (serie.length < 2) {
            alert('La serie debe tener al menos 2 valores.');
            return false;
        }
        document.getElementById('valores-hidden').value = serie.join(',');
        return true;
    }

    // 🔹 Embudo para serie agrupada
    let clases = [];

    function agregarClase() {
        const limInf = parseFloat(document.getElementById('lim_inf').value);
        const limSup = parseFloat(document.getElementById('lim_sup').value);
        const frecuencia = parseInt(document.getElementById('frecuencia').value);

        if (isNaN(limInf) || isNaN(limSup) || isNaN(frecuencia) || limInf >= limSup || frecuencia <= 0) {
            alert('Datos inválidos. Verifica los límites y la frecuencia.');
            return;
        }

        clases.push({ lim_inf: limInf, lim_sup: limSup, frecuencia });
        actualizarTabla();
        limpiarInputs();
    }

    function actualizarTabla() {
        const tbody = document.querySelector('#tabla-clases tbody');
        tbody.innerHTML = '';
        clases.forEach((c, i) => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${c.lim_inf}</td>
                <td>${c.lim_sup}</td>
                <td>${c.frecuencia}</td>
            `;
            tbody.appendChild(fila);
        });
    }

    function limpiarInputs() {
        document.getElementById('lim_inf').value = '';
        document.getElementById('lim_sup').value = '';
        document.getElementById('frecuencia').value = '';
        document.getElementById('lim_inf').focus();
    }

    function prepararEnvioAgrupada() {
        if (clases.length < 2) {
            alert('Debes ingresar al menos dos clases agrupadas.');
            return false;
        }
        document.getElementById('clases-json').value = JSON.stringify(clases);
        return true;
    }
</script>
@endsection

