<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPersonal extends Model
{
    protected $table = 'listas_personales'; 

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'estado',
    ];

    // Una lista pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una lista pertenece a una película
    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class);
    }

}