<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coleccion extends Model
{
    protected $table = 'colecciones'; 

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'publica'
    ];
}