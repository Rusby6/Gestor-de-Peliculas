<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PeliculaController extends Controller
{
    public function buscar(Request $request)
    {
        if (!$request->filled('titulo')) {
            return view('peliculas.buscar');
        }

        $token = env('TMDB_TOKEN');

        $response = Http::withToken($token)->get('https://api.themoviedb.org/3/search/movie', ['language' => 'es-ES','query' => $request->titulo,'year' => $request->anio,]);

        if ($response->failed()) {
            return view('peliculas.buscar');
        }

        $resultados = $response->json()['results'] ?? [];

        return view('peliculas.buscar', [
            'resultados' => $resultados,
            'busqueda' => $request->titulo
        ]);
    }
}