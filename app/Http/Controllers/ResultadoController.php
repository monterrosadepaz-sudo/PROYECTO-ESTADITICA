<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\ResultadoEstadistico;
use App\Models\modelos\SesionEstadistica;
use App\Models\modelos\DatosSimplesHist;
use App\Models\modelos\ClaseAgrupadaHist;
use App\Models\modelos\CalculosEstadisticos;

class ResultadoController extends Controller
{
    public function calcular($sesion_id)
    {
        $sesion = SesionEstadistica::findOrFail($sesion_id);

        if ($sesion->tipo_serie === 'simple') {
            $valores = DatosSimplesHist::where('sesion_id', $sesion_id)->pluck('valor')->toArray();
        } else {
            $valores = ClaseAgrupadaHist::where('sesion_id', $sesion_id)->get(); // se procesan diferente
        }

        $resultados = CalculosEstadisticos::procesar($valores, $sesion->tipo_serie);

        ResultadoEstadistico::create(array_merge($resultados, [
            'sesion_id' => $sesion_id,
        ]));

        return redirect()->back()->with('success', 'Resultados calculados y guardados');
    }
}

