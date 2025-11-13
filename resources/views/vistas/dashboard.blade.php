@extends('vistas.app')

@section('contenido')
<div class="container mt-5 text-white">
    <h1 class="mb-4">Bienvenido al Dashboard Estadístico</h1>

    <p class="text-muted">Define el nombre de la sesión y el tipo de datos que vas a ingresar.</p>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Mensajes de error --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para iniciar sesión estadística --}}
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

    {{-- Formulario para ingresar datos según tipo de serie --}}
    @if(session('sesion_id'))
        <div class="card bg-dark mb-4">
            <div class="card-header">Ingreso de datos para sesión activa</div>
            <div class="card-body">
                @if(session('tipo_serie') === 'simple')
                    <form action="{{ route('datos.simples.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="number" name="valor" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar valor</button>
                    </form>
                @elseif(session('tipo_serie') === 'agrupada')
                    <form action="{{ route('clases.agrupadas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="lim_inf" class="form-label">Límite inferior</label>
                            <input type="number" name="lim_inf" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="lim_sup" class="form-label">Límite superior</label>
                            <input type="number" name="lim_sup" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="frecuencia" class="form-label">Frecuencia</label>
                            <input type="number" name="frecuencia" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar clase</button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Listado de sesiones activas --}}
    <div class="card bg-dark">
        <div class="card-header">Sesiones registradas</div>
        <div class="card-body">
            @if(isset($sesiones) && $sesiones->count())
                @foreach($sesiones as $sesion)
                    <div class="mb-3 border-bottom pb-2">
                        <strong>{{ $sesion->nombre_clave }}</strong> ({{ $sesion->tipo_serie }})
                        <a href="{{ route('sesion.show', $sesion->id) }}" class="btn btn-sm btn-outline-info ms-2">Ver</a>
                        <form action="{{ route('resultados.calcular', $sesion->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Calcular</button>
                        </form>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No hay sesiones registradas aún.</p>
            @endif
        </div>
    </div>
</div>
@endsection

