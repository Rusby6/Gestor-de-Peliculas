<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'foto_perfil',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Un usuario tiene muchas listas personales
    public function listasPersonales()
    {
        return $this->hasMany(ListaPersonal::class);
    }

    // Un usuario tiene muchas reseñas
    public function resenas()
    {
        return $this->hasMany(Resena::class);
    }

    // Un usuario tiene muchas colecciones
    public function colecciones()
    {
        return $this->hasMany(Coleccion::class);
    }

    // Películas que el usuario tiene en sus listas
    public function peliculasEnListas()
    {
        return $this->belongsToMany(Pelicula::class, 'listas_personales')->withPivot('tipo')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

}
