<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class ClaseAgrupadaHist extends Model
{
    protected $table = 'clases_agrupadas_hist';

    protected $fillable = [
        'limite_inferior',
        'limite_superior',
        'frecuencia',
        'sesion_id',
    ];
}
