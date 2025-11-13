<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\ResultadoEstadistico;
use App\Models\modelos\SesionEstadistica;
use App\Models\modelos\DatosSimplesHist;
use App\Models\modelos\ClaseAgrupadaHist;
use App\Servicios\EstadisticaService;

class ResultadoController extends Controller
{
    public function calcular($sesion_id)
    {
        $sesion = SesionEstadistica::findOrFail($sesion_id);

        if ($sesion->tipo_serie === 'simple') {
            $valores = DatosSimplesHist::where('sesion_id', $sesion_id)->pluck('valor')->toArray();

            $resultados = [
                'media' => EstadisticaService::mediaSimple($valores),
                'mediana' => EstadisticaService::medianaSimple($valores),
                'moda' => implode(', ', EstadisticaService::modaSimple($valores)),
                'varianza' => EstadisticaService::varianzaSimple($valores),
            ];
        } else {
            $clases = ClaseAgrupadaHist::where('sesion_id', $sesion_id)->get();
            $total = $clases->sum('frecuencia');

            $clasesProcesadas = $clases->map(function ($clase) use ($total) {
                $marca = EstadisticaService::puntoMedio($clase->lim_inf, $clase->lim_sup);
                return [
                    'lim_inf' => $clase->lim_inf,
                    'lim_sup' => $clase->lim_sup,
                    'marca' => $marca,
                    'frecuencia' => $clase->frecuencia,
                    'pmf' => EstadisticaService::productoMarcaPorFrecuencia($marca, $clase->frecuencia),
                    'frecuencia_relativa' => EstadisticaService::frecuenciaRelativa($clase->frecuencia, $total),
                ];
            })->toArray();

            $resultados = [
                'media' => EstadisticaService::mediaAgrupada($clasesProcesadas),
                'mediana' => EstadisticaService::medianaAgrupada($clasesProcesadas),
                'moda' => EstadisticaService::modaAgrupada($clasesProcesadas),
                'varianza' => EstadisticaService::varianzaAgrupada($clasesProcesadas),
            ];
        }

        ResultadoEstadistico::create(array_merge($resultados, [
            'sesion_id' => $sesion_id,
        ]));

        return redirect()->back()->with('success', 'Resultados calculados y guardados');
    }
}


