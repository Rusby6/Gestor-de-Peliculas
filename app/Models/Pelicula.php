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
        'poster_path',
    ];

    /**
     * Una película tiene muchas reseñas
     */
    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    /**
     * Una película está en muchas listas personales
     */
    public function listasPersonales()
    {
        return $this->hasMany(ListaPersonal::class);
    }

    /**
     * Una película pertenece a muchas colecciones
     */
    public function colecciones()
    {
        return $this->belongsToMany(Coleccion::class, 'coleccion_pelicula')->withTimestamps();
    }

    /**
     * Una película pertenece a muchos géneros
     */
        public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_pelicula')->withTimestamps();
    }

    /**
     * Calcular la media de valoraciones de la película
     */
    public function mediaValoraciones()
    {
        return $this->resenas()->avg('puntuacion') ?? 0;
    }

    /**
     * Obtener el número total de valoraciones
     */
    public function totalValoraciones()
    {
        return $this->resenas()->count();
    }

    /**
     * Verificar si un usuario ya valoró esta película
     */
    public function valoradaPorUsuario($userId)
    {
        return $this->resenas()->where('user_id', $userId)->exists();
    }

    /**
     * Obtener la valoración de un usuario específico
     */
    public function valoracionDeUsuario($userId)
    {
        return $this->resenas()->where('user_id', $userId)->first();
    }
}