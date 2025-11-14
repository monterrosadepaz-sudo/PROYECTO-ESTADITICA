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
        'valores' => 'required|string',
    ]);

    $sesionId = session('sesion_id');

    if (!$sesionId) {
        return redirect()->back()->withErrors(['sesion_id' => 'No hay sesión activa']);
    }

    $valores = preg_split('/[\s,]+/', $request->valores);
    $valores = array_filter($valores, fn($v) => is_numeric($v));

    if (count($valores) < 2) {
        return redirect()->back()->withErrors(['valores' => 'La serie debe tener al menos 2 valores.']);
    }

    foreach ($valores as $valor) {
        $dato = DatosSimplesRT::create([
            'valor' => floatval($valor),
        ]);

        DatosSimplesHist::create([
            'valor' => $dato->valor,
            'sesion_id' => $sesionId,
        ]);
    }

    return redirect()->back()->with('success', 'Serie registrada y clonada correctamente');
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
