<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MiListaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $peliculas = Auth::user()->miLista()->latest()->get();
        return view('mi-lista.index', compact('peliculas'));
    }
    
    public function guardar(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'titulo' => 'required|string',
            'poster_path' => 'nullable|string',
            'anio' => 'nullable|string',
        ]);
        
        MiLista::create([
            'user_id' => Auth::id(),
            'tmdb_id' => $request->tmdb_id,
            'titulo' => $request->titulo,
            'poster_path' => $request->poster_path,
            'anio' => $request->anio,
        ]);
        
    }
    
    public function eliminar($id)
        {
            $pelicula = MiLista::find($id);
            $pelicula->delete();
            
            return back();
        }
}
