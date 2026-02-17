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
        'visibillidad'
    ];

    protected $casts = [
        'visibilidad' => 'boolean',
    ];

    // Una colección pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una colección tiene muchas películas (relación N-N)
    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class, 'coleccion_peliculas')->withTimestamps();
    }

}