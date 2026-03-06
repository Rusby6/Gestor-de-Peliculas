<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buscar Películas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 border border-red-400 rounded-md p-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Buscador -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('peliculas.buscar') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Búsqueda por título -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                <input type="text" 
                                       name="titulo" 
                                       value="{{ request('titulo') }}"
                                       placeholder="Ej: Batman"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <!-- Ordenación -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                                <select name="orden" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="relevancia" {{ request('orden') == 'relevancia' ? 'selected' : '' }}>Relevancia</option>
                                    <option value="titulo" {{ request('orden') == 'titulo' ? 'selected' : '' }}>Título</option>
                                    <option value="anio" {{ request('orden') == 'anio' ? 'selected' : '' }}>Año</option>
                                    <option value="valoracion" {{ request('orden') == 'valoracion' ? 'selected' : '' }}>Valoración</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('peliculas.buscar') }}" 
                               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
                                Limpiar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resultados -->
            @if(isset($resultados) && count($resultados) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Resultados para "{{ $busqueda }}"
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($resultados as $pelicula)
                                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                    @if(!empty($pelicula['poster_path']))
                                        <img src="https://image.tmdb.org/t/p/w500{{ $pelicula['poster_path'] }}" 
                                            class="w-full h-auto aspect-[2/3] object-cover" 
                                            alt="{{ $pelicula['title'] }}"
                                            onerror="this.onerror=null; this.src='https://via.placeholder.com/500x750?text=Sin+imagen';">
                                    @else
                                        <div class="w-full h-auto aspect-[2/3] bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg">{{ $pelicula['title'] }}</h3>
                                        
                                        <div class="flex justify-between items-center mb-2">
                                            <p class="text-sm text-gray-600">
                                                {{ substr($pelicula['release_date'] ?? '', 0, 4) }}
                                            </p>
                                            <p class="text-sm text-yellow-600">
                                                ⭐ {{ number_format($pelicula['vote_average'] ?? 0, 1) }}
                                            </p>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-4">
                                            {{ Str::limit($pelicula['overview'] ?? 'Sin descripción', 100) }}
                                        </p>
                                        
                                        <form method="POST" action="{{ route('peliculas.importar') }}">
                                            @csrf
                                            <input type="hidden" name="tmdb_id" value="{{ $pelicula['id'] }}">
                                            <input type="hidden" name="titulo" value="{{ $pelicula['title'] }}">
                                            <input type="hidden" name="poster_path" value="{{ $pelicula['poster_path'] ?? '' }}">
                                            <input type="hidden" name="anio" value="{{ substr($pelicula['release_date'] ?? '', 0, 4) }}">
                                            <input type="hidden" name="sinopsis" value="{{ $pelicula['overview'] ?? '' }}">
                                            <input type="hidden" name="duracion" value="{{ $pelicula['runtime'] ?? '' }}">
                                            <input type="hidden" name="generos" value="{{ $pelicula['generos_nombres'] ?? '' }}">
                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Importar al catálogo
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($totalPages > 1)
                            <div class="mt-6 flex justify-center gap-2">
                                @for($i = 1; $i <= $totalPages; $i++)
                                    <a href="{{ route('peliculas.buscar', ['titulo' => $busqueda, 'page' => $i, 'orden' => request('orden')]) }}" 
                                       class="px-3 py-1 border rounded {{ $i == $currentPage ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </div>
                        @endif
                    </div>
                </div>
            @elseif(isset($resultados))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        No se encontraron películas para "{{ $busqueda }}"
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>