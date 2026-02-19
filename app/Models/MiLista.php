<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiLista extends Model
{
    protected $table = 'mi_lista';
    
    protected $fillable = [
        'user_id',
        'tmdb_id',
        'titulo',
        'poster_path',
        'anio',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
