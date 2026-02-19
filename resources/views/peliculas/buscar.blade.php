<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buscar Películas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <form method="GET" action="{{ route('peliculas.buscar') }}">
                        <input type="text" name="titulo" value="{{ request('titulo') }}" placeholder="Título de la película..."class="border rounded px-4 py-2 w-full">
                    
                        <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>
            
            @if(isset($resultados))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Resultados para "{{ $busqueda }}"
                        </h3>
                        
                        @forelse($resultados as $pelicula)
                            <div class="border-b border-gray-200 py-4 last:border-0">
                                <div class="flex gap-4">
                                    @if(!empty($pelicula['poster_path']))
                                        <img src="https://image.tmdb.org/t/p/w92{{ $pelicula['poster_path'] }}" 
                                            class="w-16 h-24 object-cover rounded">
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h4 class="font-semibold">
                                            {{ $pelicula['title'] }}
                                            <span class="text-gray-500 text-sm">
                                                ({{ $pelicula['release_date'] }})
                                            </span>
                                        </h4>
                                        
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $pelicula['overview'] }}
                                        </p>
                                        
                                        <div class="mt-2 flex items-center gap-4">
                                            <span class="text-sm">
                                                ⭐ {{ $pelicula['vote_average'] }}
                                            </span>
                                            
                                            <form method="POST" action="{{ route('mi-lista.guardar') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="tmdb_id" value="{{ $pelicula['id'] }}">
                                                <input type="hidden" name="titulo" value="{{ $pelicula['title'] }}">
                                                <input type="hidden" name="poster_path" value="{{ $pelicula['poster_path'] ?? '' }}">
                                                <input type="hidden" name="anio" value="{{ substr($pelicula['release_date'] ?? '', 0, 4) }}">
                                                <button type="submit" 
                                                        class="text-sm bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                                                    + Añadir a mi lista
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-600">No se encontraron películas.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>