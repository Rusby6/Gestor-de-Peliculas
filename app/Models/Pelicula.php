<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $fillable = [
        'tmdb_id',
        'titulo',
        'anio',
        'sinopsis',
        'duracion',
        'generos',
        'poster_path',
    ];
}
