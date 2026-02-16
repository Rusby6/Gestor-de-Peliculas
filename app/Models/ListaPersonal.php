<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaPersonal extends Model
{
    protected $table = 'listas_personales'; 

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'tipo'
    ];
}