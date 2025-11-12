<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class SesionEstadistica extends Model
{
    protected $table = 'sesiones_estadisticas';

    protected $fillable = [
        'nombre_clave',
        'tipo_serie',
    ];
}
