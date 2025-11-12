<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\DatosSimplesRT;
use App\Models\modelos\DatosSimplesHist;

class DatosSimplesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'valor' => 'required|numeric',
            'sesion_id' => 'required|integer',
        ]);

        DatosSimplesRT::create([
            'valor' => $request->valor,
            'sesion_id' => $request->sesion_id,
        ]);

        return redirect()->back()->with('success', 'Dato registrado en tiempo real');
    }

    public function clonar($sesion_id)
    {
        $datos = DatosSimplesRT::where('sesion_id', $sesion_id)->get();

        foreach ($datos as $dato) {
            DatosSimplesHist::create([
                'valor' => $dato->valor,
                'sesion_id' => $dato->sesion_id,
            ]);
        }

        return redirect()->back()->with('success', 'Datos clonados al histórico');
    }
}
