<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $fillable = [
        'nombre', 
        'slug',
    ];
    
    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class, 'genero_pelicula');
    }
}
