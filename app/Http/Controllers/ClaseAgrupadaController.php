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
            'limite_inferior' => 'required|numeric',
            'limite_superior' => 'required|numeric',
            'frecuencia' => 'required|integer',
            'sesion_id' => 'required|integer',
        ]);

        ClaseAgrupadaRT::create([
            'limite_inferior' => $request->limite_inferior,
            'limite_superior' => $request->limite_superior,
            'frecuencia' => $request->frecuencia,
            'sesion_id' => $request->sesion_id,
        ]);

        return redirect()->back()->with('success', 'Clase registrada en tiempo real');
    }

    public function clonar($sesion_id)
    {
        $clases = ClaseAgrupadaRT::where('sesion_id', $sesion_id)->get();

        foreach ($clases as $clase) {
            ClaseAgrupadaHist::create([
                'limite_inferior' => $clase->limite_inferior,
                'limite_superior' => $clase->limite_superior,
                'frecuencia' => $clase->frecuencia,
                'sesion_id' => $clase->sesion_id,
            ]);
        }

        return redirect()->back()->with('success', 'Clases clonadas al histórico');
    }
}

