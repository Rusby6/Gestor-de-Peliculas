<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'puntuacion',
        'texto',    
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class);
    }
}