<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class ClaseAgrupadaRT extends Model
{
    protected $table = 'clases_agrupadas_rt';

    protected $fillable = [
        'limite_inferior',
        'limite_superior',
        'frecuencia',
        'sesion_id',
    ];
}
