<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class ResultadoEstadistico extends Model
{
    protected $table = 'resultados_estadisticos';

    protected $fillable = [
        'sesion_id',
        'media',
        'mediana',
        'moda',
        'rango',
        'varianza',
        'desviacion',
        'cuartiles',
        'deciles',
        'percentiles',
    ];
}
