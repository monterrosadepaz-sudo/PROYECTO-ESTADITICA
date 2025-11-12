<?php

namespace App\Models\modelos;

use Illuminate\Database\Eloquent\Model;

class DatosSimplesRT extends Model
{
    protected $table = 'datos_simples_rt';

    protected $fillable = [
        'valor',
        'sesion_id',
    ];
}
