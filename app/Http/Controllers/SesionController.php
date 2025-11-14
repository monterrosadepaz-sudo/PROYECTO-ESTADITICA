<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\SesionEstadistica;
use App\Models\modelos\DatosSimplesHist;
use App\Models\modelos\ClaseAgrupadaHist;
use App\Models\modelos\ClaseAgrupadaRT;
use App\Servicios\EstadisticaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class SesionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre_clave' => 'required|string|max:255',
            'tipo_serie' => 'required|in:simple,agrupada',
        ]);

        $sesion = SesionEstadistica::create([
            'nombre_clave' => $request->nombre_clave,
            'tipo_serie' => $request->tipo_serie,
        ]);

        session([
            'sesion_id' => $sesion->id,
            'tipo_serie' => $sesion->tipo_serie,
        ]);

        return redirect()->route('dashboard')->with('success', 'Sesión iniciada correctamente');
    }

    public function index()
    {
        $sesiones = SesionEstadistica::latest()->get();
        $clasesRT = ClaseAgrupadaRT::latest()->get();

        $sesion = null;
        if (session()->has('sesion_id')) {
            $sesion = SesionEstadistica::find(session('sesion_id'));
        }

        return view('vistas.dashboard', compact('sesiones'));
    }

    public function show($id)
    {
        $sesion = SesionEstadistica::findOrFail($id);

        if ($sesion->tipo_serie === 'simple') {
            $valores = DatosSimplesHist::where('sesion_id', $id)->pluck('valor')->toArray();

            $estadisticas = count($valores) > 0 ? [
                'media' => EstadisticaService::media($valores),
                'mediana' => EstadisticaService::mediana($valores),
                'moda' => EstadisticaService::moda($valores),
                'varianza' => EstadisticaService::varianza($valores),
                'desviacion' => EstadisticaService::desviacionEstandar($valores),
            ] : null;

            return view('vistas.resultados', compact('sesion', 'valores', 'estadisticas'));
        }

        if ($sesion->tipo_serie === 'agrupada') {
            $clasesRaw = ClaseAgrupadaHist::where('sesion_id', $id)->get();
            $total = $clasesRaw->sum('frecuencia');

            $clases = $clasesRaw->map(function ($clase) use ($total) {
                $marca = EstadisticaService::puntoMedio($clase->limite_inferior, $clase->limite_superior);
                return [
                    'lim_inf' => $clase->limite_inferior,
                    'lim_sup' => $clase->limite_superior,
                    'frecuencia' => $clase->frecuencia,
                    'marca' => $marca,
                    'pmf' => EstadisticaService::productoMarcaPorFrecuencia($marca, $clase->frecuencia),
                    'frecuencia_relativa' => EstadisticaService::frecuenciaRelativa($clase->frecuencia, $total),
                ];
            })->toArray();

            $clases = EstadisticaService::frecuenciaAcumulada($clases);

            $estadisticas = count($clases) > 0 ? [
                'media' => EstadisticaService::mediaAgrupada($clases),
                'mediana' => EstadisticaService::medianaAgrupada($clases),
                'moda' => EstadisticaService::modaAgrupada($clases),
                'varianza' => EstadisticaService::varianzaAgrupada($clases),
                'desviacion' => EstadisticaService::desviacionAgrupada($clases),
            ] : null;

            return view('vistas.resultados', compact('sesion', 'clases', 'estadisticas'));
        }

        return redirect()->route('dashboard')->withErrors(['tipo_serie' => 'Tipo de serie no reconocido']);
    }

    public function reportePDF($id): Response
    {
        $sesion = SesionEstadistica::findOrFail($id);

        if ($sesion->tipo_serie === 'simple') {
            $valores = DatosSimplesHist::where('sesion_id', $id)->pluck('valor')->toArray();

            $estadisticas = count($valores) > 0 ? [
                'media' => EstadisticaService::media($valores),
                'mediana' => EstadisticaService::mediana($valores),
                'moda' => EstadisticaService::moda($valores),
                'varianza' => EstadisticaService::varianza($valores),
                'desviacion' => EstadisticaService::desviacionEstandar($valores),
            ] : null;

            $pdf = Pdf::loadView('vistas.resultados', compact('sesion', 'valores', 'estadisticas'));
            return $pdf->download('reporte_sesion_'.$sesion->id.'.pdf');
        }

        if ($sesion->tipo_serie === 'agrupada') {
            $clasesRaw = ClaseAgrupadaHist::where('sesion_id', $id)->get();
            $total = $clasesRaw->sum('frecuencia');

            $clases = $clasesRaw->map(function ($clase) use ($total) {
                $marca = EstadisticaService::puntoMedio($clase->limite_inferior, $clase->limite_superior);
                return [
                    'lim_inf' => $clase->limite_inferior,
                    'lim_sup' => $clase->limite_superior,
                    'frecuencia' => $clase->frecuencia,
                    'marca' => $marca,
                    'pmf' => EstadisticaService::productoMarcaPorFrecuencia($marca, $clase->frecuencia),
                    'frecuencia_relativa' => EstadisticaService::frecuenciaRelativa($clase->frecuencia, $total),
                ];
            })->toArray();

            $clases = EstadisticaService::frecuenciaAcumulada($clases);

            $estadisticas = count($clases) > 0 ? [
                'media' => EstadisticaService::mediaAgrupada($clases),
                'mediana' => EstadisticaService::medianaAgrupada($clases),
                'moda' => EstadisticaService::modaAgrupada($clases),
                'varianza' => EstadisticaService::varianzaAgrupada($clases),
                'desviacion' => EstadisticaService::desviacionAgrupada($clases),
            ] : null;

            $pdf = Pdf::loadView('vistas.resultados', compact('sesion', 'clases', 'estadisticas'));
            return $pdf->download('reporte_sesion_'.$sesion->id.'.pdf');
        }

        return response('Tipo de serie no reconocido', 400);
    }

    public function cerrar(Request $request)
    {
        $request->session()->forget(['sesion_id', 'tipo_serie']);
        return redirect()->route('dashboard')->with('success', 'Sesión cerrada correctamente');
    }
}

