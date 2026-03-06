<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catálogo de Películas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 text-sm text-green-600 bg-green-100 border border-green-400 rounded-md p-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- FILTROS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('peliculas.index') }}" class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Búsqueda -->
                            <div class="flex-1 min-w-[200px]">
                                <div class="relative">
                                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                                           placeholder="Buscar película..."
                                           class="w-full pl-8 pr-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Año -->
                            <select name="anio" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 min-w-[100px]">
                                <option value="">Año</option>
                                @foreach($anios ?? [] as $año)
                                    <option value="{{ $año }}" {{ request('anio') == $año ? 'selected' : '' }}>{{ $año }}</option>
                                @endforeach
                            </select>

                            <!-- Género -->
                            <select name="genero" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 min-w-[120px]">
                                <option value="">Género</option>
                                @php
                                    $generos = ['Acción', 'Aventura', 'Animación', 'Comedia', 'Drama', 'Terror', 'Ciencia ficción', 'Romance'];
                                @endphp
                                @foreach($generos as $genero)
                                    <option value="{{ $genero }}" {{ request('genero') == $genero ? 'selected' : '' }}>{{ $genero }}</option>
                                @endforeach
                            </select>

                            <!-- Orden -->
                            <select name="orden" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 min-w-[130px]">
                                <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
                                <option value="titulo" {{ request('orden') == 'titulo' ? 'selected' : '' }}>Título (A-Z)</option>
                                <option value="titulo_desc" {{ request('orden') == 'titulo_desc' ? 'selected' : '' }}>Título (Z-A)</option>
                                <option value="anio" {{ request('orden') == 'anio' ? 'selected' : '' }}>Año ↓</option>
                                <option value="anio_asc" {{ request('orden') == 'anio_asc' ? 'selected' : '' }}>Año ↑</option>
                                <option value="valoracion" {{ request('orden') == 'valoracion' ? 'selected' : '' }}>Valoración</option>
                            </select>

                            @auth
                                <select name="estado" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 min-w-[120px]">
                                    <option value="">Mi lista</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>⏳ Pendientes</option>
                                    <option value="vista" {{ request('estado') == 'vista' ? 'selected' : '' }}>✓ Vistas</option>
                                    <option value="favorita" {{ request('estado') == 'favorita' ? 'selected' : '' }}>♥ Favoritas</option>
                                </select>
                            @endauth

                            <div class="flex gap-1">
                                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm whitespace-nowrap">Filtrar</button>
                                @if(request()->anyFilled(['buscar', 'anio', 'genero', 'orden', 'estado']))
                                    <a href="{{ route('peliculas.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm whitespace-nowrap">✕</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!--  BOTÓN CREAR PELÍCULA (solo admin) -->
            @can('admin-only')
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('peliculas.create') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        + Crear Película
                    </a>
                </div>
            @endcan

            <!-- Listado de películas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($peliculas->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($peliculas as $pelicula)
                                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                                    @if($pelicula->poster_path)
                                        @php
                                            $imageUrl = $pelicula->poster_path;
                                            if (!str_starts_with($imageUrl, 'http')) {
                                                $imageUrl = 'https://image.tmdb.org/t/p/w500' . $imageUrl;
                                            }
                                        @endphp
                                        <img src="{{ $imageUrl }}"
                                             class="w-full h-auto aspect-[2/3] object-cover"
                                             alt="{{ $pelicula->titulo }}"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/500x750?text=Sin+imagen';">
                                    @else
                                        <div class="w-full h-auto aspect-[2/3] bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg">{{ $pelicula->titulo }}</h3>

                                        <div class="flex justify-between items-center mb-2">
                                            <p class="text-sm text-gray-600">{{ $pelicula->anio }}</p>
                                            <p class="text-sm text-yellow-600">⭐ {{ number_format($pelicula->mediaValoraciones(), 1) }}</p>
                                        </div>

                                        <!-- Géneros -->
                                        @if($pelicula->generos && $pelicula->generos->count() > 0)
                                            <p class="text-sm text-gray-600 mb-2">
                                                <span class="font-semibold">Géneros:</span> 
                                                {{ $pelicula->generos->pluck('nombre')->implode(', ') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-400 italic mb-2">Géneros no disponibles</p>
                                        @endif

                                        <div class="flex flex-col gap-2 mt-3">
                                            <a href="{{ route('peliculas.show', $pelicula) }}" class="text-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded text-sm">
                                                Ver detalles
                                            </a>

                                            @auth
                                                <div class="flex gap-2">
                                                    <form method="POST" action="{{ route('listas.agregar', $pelicula) }}" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="pendiente">
                                                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded text-sm">⏳ Pendiente</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('listas.agregar', $pelicula) }}" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="vista">
                                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-3 rounded text-sm">✓ Vista</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('listas.agregar', $pelicula) }}" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="favorita">
                                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded text-sm">♥ Favorita</button>
                                                    </form>
                                                </div>

                                                @php $enLista = $pelicula->listasPersonales()->where('user_id', Auth::id())->exists(); @endphp
                                                @if($enLista)
                                                    <form method="POST" action="{{ route('listas.quitar', $pelicula) }}">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-3 rounded text-sm" onclick="return confirm('¿Quitar de todas tus listas?')">Quitar de listas</button>
                                                    </form>
                                                @endif

                                                <!-- 🔴 BOTONES DE ADMIN (solo visibles para admin) -->
                                                @can('admin-only')
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        <div class="flex gap-2">
                                                            <a href="{{ route('peliculas.edit', $pelicula) }}" 
                                                               class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded text-sm">
                                                                ✏️ Editar
                                                            </a>
                                                            <form method="POST" action="{{ route('peliculas.destroy', $pelicula) }}"
                                                                  onsubmit="return confirm('¿Eliminar esta película del catálogo?');" class="flex-1">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm">
                                                                    🗑️ Eliminar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endcan
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">{{ $peliculas->links() }}</div>
                    @else
                        <p class="text-center py-8">
                            No hay películas en el catálogo.
                            <a href="{{ route('peliculas.buscar') }}" class="text-indigo-600 hover:underline">Importa desde TMDB</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>