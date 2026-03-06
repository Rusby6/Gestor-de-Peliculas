<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelicula;
use App\Models\Genero;

class PeliculaController extends Controller
{
    /**
     * MOSTRAR CATÁLOGO CON FILTROS
     * Ruta: GET /peliculas
     */
    public function index(Request $request)
    {
        // =============================================
        // 1. CONSTRUIR CONSULTA CON FILTROS
        // =============================================
        $query = Pelicula::query();
        
        // Filtro por título
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }
        
        // Filtro por año
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }
        
        // Filtro por género (usa la relación con tabla pivote)
        if ($request->filled('genero')) {
            $query->whereHas('generos', function($consulta) use ($request) {
                $consulta->where('nombre', $request->genero);
            });
        }
        
        // Filtro por estado (pendiente/vista/favorita) - solo para usuarios logueados
        if ($request->filled('estado') && Auth::check()) {
            $estado = $request->estado;
            $userId = Auth::id();
            $query->whereHas('listasPersonales', function($consulta) use ($userId, $estado) {
                $consulta->where('user_id', $userId)->where('estado', $estado);
            });
        }
        
        // =============================================
        // 2. ORDENACIÓN
        // =============================================
        $orden = $request->input('orden', 'reciente'); // Por defecto 'reciente'
        
        switch ($orden) {
            case 'titulo':
                $query->orderBy('titulo', 'asc');
                break;
            case 'titulo_desc':
                $query->orderBy('titulo', 'desc');
                break;
            case 'anio':
                $query->orderBy('anio', 'desc');
                break;
            case 'anio_asc':
                $query->orderBy('anio', 'asc');
                break;
            case 'valoracion':
                // Calcula media de puntuaciones y ordena por ella
                $query->withAvg('resenas', 'puntuacion')
                      ->orderBy('resenas_avg_puntuacion', 'desc');
                break;
            default: // 'reciente'
                $query->latest(); // ordena por created_at desc
                break;
        }
        
        // =============================================
        // 3. EJECUTAR CONSULTA
        // =============================================
        // Carga los géneros de cada película para evitar consultas adicionales
        $peliculas = $query->with('generos')                // UNA SOLA CONSULTA extra para traer TODOS los géneros de TODAS las películas.
                           ->paginate(12)                 // 12 por página
                           ->withQueryString();           // Mantiene filtros en paginación
        
        // =============================================
        // 4. OBTENER AÑOS PARA EL FILTRO
        // =============================================
        $anios = Pelicula::select('anio')
                         ->distinct()                    // Años únicos
                         ->whereNotNull('anio')          // Sin nulos
                         ->orderBy('anio', 'desc')       // Más reciente primero
                         ->pluck('anio');                 // Solo el campo anio
        
        // =============================================
        // 5. ENVIAR A LA VISTA
        // =============================================
        return view('peliculas.index', compact('peliculas', 'anios'));
    }

    /**
     * BUSCAR PELÍCULAS EN TMDB
     * Ruta: GET /peliculas/buscar
     */
    public function buscar(Request $request)
    {
        // =============================================
        // 1. CONFIGURACIÓN INICIAL
        // =============================================
        $token = env('TMDB_TOKEN'); // Token de la API (en .env)
        
        // Si no hay término de búsqueda, mostrar formulario vacío
        if (!$request->filled('titulo')) {
            return view('peliculas.buscar', [
                'resultados' => [],
                'busqueda' => '',
                'totalPages' => 0,
                'currentPage' => 1
            ]);
        }
        
        $busqueda = $request->titulo; // Término a buscar
        $pagina = $request->input('page', 1); // Página actual (para paginación)
        
        // =============================================
        // 2. OBTENER LISTA DE GÉNEROS DE TMDB
        // =============================================
        $generosResponse = Http::withToken($token)
            ->get('https://api.themoviedb.org/3/genre/movie/list', [
                'language' => 'es-ES'
            ]);
        
        // Crear un mapa: ID del género => Nombre del género
        $mapaGeneros = [];
        if ($generosResponse->successful()) {
            foreach ($generosResponse->json()['genres'] as $genero) {
                $mapaGeneros[$genero['id']] = $genero['name'];
            }
        }
        
        // =============================================
        // 3. BUSCAR PELÍCULAS EN TMDB
        // =============================================
        $response = Http::withToken($token)
            ->get('https://api.themoviedb.org/3/search/movie', [
                'language' => 'es-ES',
                'query' => $busqueda,
                'page' => $pagina,
            ]);
        
        // Si hay error de conexión
        if ($response->failed()) {
            return view('peliculas.buscar', [
                'error' => 'Error al conectar con TMDB',
                'resultados' => [],
                'busqueda' => $busqueda,
                'totalPages' => 0,
                'currentPage' => 1
            ]);
        }

        // =============================================
        // 4. PROCESAR RESULTADOS
        // =============================================
        $datos = $response->json();
        $resultados = $datos['results'] ?? [];
        $totalPages = $datos['total_pages'] ?? 1;
        
        // Convertir IDs de géneros a nombres para mostrar
        foreach ($resultados as &$pelicula) {   // &$pelicula → PASO POR REFERENCIA
            $nombresGeneros = [];
            
            if (isset($pelicula['genre_ids']) && is_array($pelicula['genre_ids'])) {
                foreach ($pelicula['genre_ids'] as $id) {
                    if (isset($mapaGeneros[$id])) {
                        $nombresGeneros[] = $mapaGeneros[$id];
                    }
                }
            }
            
            // Añadir campo 'generos_nombres' a cada película
            $pelicula['generos_nombres'] = implode(', ', $nombresGeneros);
            $pelicula['runtime'] = null; // La duración no viene en búsqueda
        }
        
        // =============================================
        // 5. ORDENAR RESULTADOS 
        // =============================================
        $orden = $request->input('orden', 'relevancia');

        switch ($orden) {
            case 'titulo':
                usort($resultados, function($a, $b) {
                    return strcmp($a['title'], $b['title']);
                });
                break;
            case 'anio':
                usort($resultados, function($a, $b) {
                    $yearA = (int)substr($a['release_date'] ?? '0000', 0, 4);
                    $yearB = (int)substr($b['release_date'] ?? '0000', 0, 4);
                    return $yearB <=> $yearA; // Más reciente primero
                });
                break;
            case 'valoracion':
                usort($resultados, function($a, $b) {
                    $votoA = $a['vote_average'] ?? 0;
                    $votoB = $b['vote_average'] ?? 0;
                    return $votoB <=> $votoA; // Mayor puntuación primero
                });
                break;
            // 'relevancia' no ordena (viene ordenado por TMDB)
        }

        $currentPage = $pagina;

        // =============================================
        // 6. ENVIAR A LA VISTA
        // =============================================
        return view('peliculas.buscar', compact('resultados', 'busqueda', 'totalPages', 'currentPage'));
    }

    /**
     * IMPORTAR PELÍCULA DE TMDB A BD LOCAL
     * Ruta: POST /peliculas/importar
     */
    public function importar(Request $request)
    {
        // =============================================
        // 1. VALIDAR DATOS DEL FORMULARIO
        // =============================================
        $request->validate([
            'tmdb_id' => 'required|integer',
            'titulo' => 'required|string',
            'anio' => 'nullable|string',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|integer',
            'generos' => 'nullable|string',
            'poster_path' => 'nullable|string',
        ]);
        
        // =============================================
        // 2. VERIFICAR SI YA EXISTE
        // =============================================
        $existe = Pelicula::where('tmdb_id', $request->tmdb_id)->exists();
        
        if ($existe) {
            return redirect()->route('peliculas.buscar')
                ->with('error', 'Esta película ya está en el catálogo');
        }
        
        // =============================================
        // 3. OBTENER DETALLES COMPLETOS DE TMDB
        // =============================================
        $token = env('TMDB_TOKEN');
        $detalleResponse = Http::withToken($token)
            ->get("https://api.themoviedb.org/3/movie/{$request->tmdb_id}", [
                'language' => 'es-ES'
            ]);
        
        $duracion = $request->duracion;
        if ($detalleResponse->successful()) {
            $detalle = $detalleResponse->json();
            $duracion = $detalle['runtime'] ?? $request->duracion;
        }
        
        // =============================================
        // 4. CREAR PELÍCULA EN BD
        // =============================================
        $pelicula = Pelicula::create([
            'tmdb_id' => $request->tmdb_id,
            'titulo' => $request->titulo,
            'anio' => $request->anio,
            'sinopsis' => $request->sinopsis,
            'duracion' => $duracion,
            'poster_path' => $request->poster_path,
        ]);
        
        // =============================================
        // 5. GUARDAR GÉNEROS EN TABLA PIVOTE
        // =============================================
        if ($request->filled('generos')) {
            // Convertir "Acción, Aventura, Comedia" a ["Acción", "Aventura", "Comedia"]
            $generosArray = explode(', ', $request->generos);
            
            foreach ($generosArray as $nombreGenero) {
                $nombreGenero = trim($nombreGenero);
                
                if (!empty($nombreGenero)) {
                    // Buscar el género en BD o crearlo si no existe
                    $genero = Genero::firstOrCreate(['nombre' => $nombreGenero]);
                    
                    // Asociar el género con la película
                    $pelicula->generos()->attach($genero->id);
                }
            }
        }
        
        // =============================================
        // 6. REDIRIGIR AL CATÁLOGO
        // =============================================
        return redirect()->route('peliculas.index')
            ->with('success', 'Película importada correctamente');
    }

    /**
     * MOSTRAR DETALLE DE UNA PELÍCULA
     * Ruta: GET /peliculas/{pelicula}
     */
    public function show(Pelicula $pelicula)
    {
        // Cargar los géneros de esta película
        $pelicula->load('generos');
        
        return view('peliculas.show', compact('pelicula'));
    }

    /**
     * FORMULARIO PARA CREAR PELÍCULA (SOLO ADMIN)
     * Ruta: GET /peliculas/create
     */
    public function create()
    {
        Gate::authorize('admin-only');
        return view('peliculas.create');
    }

    

    /**
     * GUARDAR NUEVA PELÍCULA (SOLO ADMIN)
     * Ruta: POST /peliculas
     */
    public function store(Request $request)
    {
        Gate::authorize('admin-only');
        
        // Validar datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duracion' => 'nullable|integer|min:1',
            'sinopsis' => 'nullable|string',
            'poster_path' => 'nullable|string',
        ]);
        
        // Crear película
        $pelicula = Pelicula::create([
            'titulo' => $request->titulo,
            'anio' => $request->anio,
            'duracion' => $request->duracion,
            'sinopsis' => $request->sinopsis,
            'poster_path' => $request->poster_path,
            // tmdb_id puede ser null porque es creación manual
        ]);
        
        return redirect()->route('peliculas.show', $pelicula)
            ->with('success', 'Película creada correctamente');
    }

    /**
     * FORMULARIO PARA EDITAR PELÍCULA (SOLO ADMIN)
     * Ruta: GET /peliculas/{pelicula}/edit
     */
    public function edit(Pelicula $pelicula)
    {
        // Solo admin puede editar
        Gate::authorize('admin-only');
        
        return view('peliculas.edit', compact('pelicula'));
    }

    /**
     * ACTUALIZAR PELÍCULA (SOLO ADMIN)
     * Ruta: PUT /peliculas/{pelicula}
     */
    public function update(Request $request, Pelicula $pelicula)
    {
        // Solo admin puede actualizar
        Gate::authorize('admin-only');
        
        // Validar datos
        $request->validate([
            'titulo' => 'required|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duracion' => 'nullable|integer|min:1',
            'sinopsis' => 'nullable|string',
            'poster_path' => 'nullable|string',
        ]);
        
        // Actualizar película
        $pelicula->update([
            'titulo' => $request->titulo,
            'anio' => $request->anio,
            'duracion' => $request->duracion,
            'sinopsis' => $request->sinopsis,
            'poster_path' => $request->poster_path,
        ]);
        
        return redirect()->route('peliculas.show', $pelicula)
            ->with('success', 'Película actualizada correctamente');
    }

    /**
     * ELIMINAR PELÍCULA (SOLO ADMIN)
     * Ruta: DELETE /peliculas/{pelicula}
     */
    public function destroy(Pelicula $pelicula)
    {
        // Solo admin puede eliminar
        Gate::authorize('admin-only');
        
        $pelicula->delete();
        
        return redirect()->route('peliculas.index')
            ->with('success', 'Película eliminada correctamente');
    }
}