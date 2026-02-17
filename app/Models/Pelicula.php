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

    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    // Una película está en muchas listas personales
    public function listasPersonales()
    {
        return $this->hasMany(ListaPersonal::class);
    }

    // Una película pertenece a muchas colecciones
    public function colecciones()
    {
        return $this->belongsToMany(Coleccion::class, 'coleccion_peliculas')->withTimestamps();
    }

    // Calcular la media de valoraciones de la película
    public function mediaValoraciones()
    {
        return $this->resenas()->avg('puntuacion');
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_pelicula');
    }
}
