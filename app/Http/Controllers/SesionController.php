<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modelos\SesionEstadistica;

class SesionController extends Controller
{
    /**
     * Guarda una nueva sesión estadística.
     */
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

        return redirect()->back()->with('success', 'Sesión iniciada correctamente');
    }

    /**
     * Muestra todas las sesiones (opcional).
     */
    public function index()
    {
        $sesiones = SesionEstadistica::latest()->get();

        return view('vistas.dashboard', compact('sesiones'));
    }

    /**
     * Muestra una sesión específica (opcional).
     */
    public function show($id)
    {
        $sesion = SesionEstadistica::findOrFail($id);

        return view('vistas.sesion_detalle', compact('sesion'));
    }
}
