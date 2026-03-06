<?php

namespace App\Http\Controllers;

use App\Models\ListaPersonal;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaPersonalController extends Controller
{
    /**
     * Mostrar las listas del usuario (pendientes, vistas, favoritas)
     */
    public function index()
    {
        $user = Auth::user();
        
        $pendientes = $user->listasPersonales()->where('estado', 'pendiente')->with('pelicula')->get();
        $vistas = $user->listasPersonales()->where('estado', 'vista')->with('pelicula')->get();
        $favoritas = $user->listasPersonales()->where('estado', 'favorita')->with('pelicula')->get();
        
        return view('listas.index', compact('pendientes', 'vistas', 'favoritas'));
    }
    
    /**
     * Añadir película a una lista (pendiente/vista/favorita)
     * Para películas ya importadas en el catálogo
     */
    public function agregar(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,vista,favorita'
        ]);
        
        $existe = ListaPersonal::where('user_id', Auth::id())
                               ->where('pelicula_id', $pelicula->id)
                               ->where('estado', $request->estado)
                               ->exists();
        
        if ($existe) {
            return back()->with('error', "La película ya está en {$request->estado}");
        }
        
        ListaPersonal::create([
            'user_id' => Auth::id(),
            'pelicula_id' => $pelicula->id,
            'estado' => $request->estado,
        ]);
        
        return back()->with('success', "Película añadida a {$request->estado}");
    }

    /**
     * Agregar película desde búsqueda de TMDB (aún no importada)
     * Primero importa la película, luego la agrega a la lista
     */
    public function agregarTemporal(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'titulo' => 'required|string',
            'poster_path' => 'nullable|string',
            'anio' => 'nullable|string',
            'estado' => 'required|in:pendiente,vista,favorita'
        ]);

        // Buscar o crear la película
        $pelicula = Pelicula::firstOrCreate(
            ['tmdb_id' => $request->tmdb_id],
            [
                'titulo' => $request->titulo,
                'poster_path' => $request->poster_path,
                'anio' => $request->anio,
            ]
        );

        // Verificar si ya está en esa lista
        $existe = ListaPersonal::where('user_id', Auth::id())
                               ->where('pelicula_id', $pelicula->id)
                               ->where('estado', $request->estado)
                               ->exists();
        
        if ($existe) {
            return back()->with('error', "La película ya está en {$request->estado}");
        }

        // Crear la lista
        ListaPersonal::create([
            'user_id' => Auth::id(),
            'pelicula_id' => $pelicula->id,
            'estado' => $request->estado,
        ]);
        
        return back()->with('success', "Película añadida a {$request->estado}");
    }
    
    /**
     * Quitar película de todas las listas
     */
    public function quitar(Request $request, Pelicula $pelicula)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,vista,favorita'
        ]);
        
        ListaPersonal::where('user_id', Auth::id())
                    ->where('pelicula_id', $pelicula->id)
                    ->where('estado', $request->estado)
                    ->delete();
        
        return back()->with('success', "Película eliminada de {$request->estado}");
    }
}