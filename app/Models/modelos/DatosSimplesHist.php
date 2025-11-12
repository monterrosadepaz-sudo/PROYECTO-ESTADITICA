<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class DatosSimplesHist extends Model
{
    protected $table = 'datos_simples_hist';

    protected $fillable = [
        'valor',
        'sesion_id',
    ];
}
