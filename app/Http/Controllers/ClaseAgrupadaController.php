<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\ClaseAgrupadaRT;
use App\Models\modelos\ClaseAgrupadaHist;

class ClaseAgrupadaController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'clases_json' => 'required|json',
        'sesion_id' => 'required|integer',
    ]);

    $clases = json_decode($request->clases_json, true);

    foreach ($clases as $clase) {
        ClaseAgrupadaRT::create([
            'limite_inferior' => $clase['lim_inf'],
            'limite_superior' => $clase['lim_sup'],
            'frecuencia' => $clase['frecuencia'],
        ]);
    }

       foreach ($clases as $clase) {
        ClaseAgrupadaHist::create([
            'limite_inferior' => $clase['lim_inf'],
            'limite_superior' => $clase['lim_sup'],
            'frecuencia' => $clase['frecuencia'],
            'sesion_id' => $request->sesion_id,
        ]);
    }

    return redirect()->route('sesion.show', $request->sesion_id)
        ->with('success', 'Serie agrupada registrada y clonada correctamente');
}



    public function clonar($sesion_id)
    {
        $clases = ClaseAgrupadaRT::where('sesion_id', $sesion_id)->get();

        foreach ($clases as $clase) {
            ClaseAgrupadaHist::create([
                'limite_inferior' => $clase->limite_inferior,
                'limite_superior' => $clase->limite_superior,
                'frecuencia' => $clase->frecuencia,
                'sesion_id' => $sesion_id,
            ]);
        }

        return redirect()->back()->with('success', 'Clases clonadas al histórico');
    }
}

