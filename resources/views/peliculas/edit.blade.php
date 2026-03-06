<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Película') }}: {{ $pelicula->titulo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('peliculas.update', $pelicula) }}">
                        @csrf
                        @method('PUT')

                        <!-- Título -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="titulo">
                                Título
                            </label>
                            <input type="text" 
                                   name="titulo" 
                                   id="titulo" 
                                   value="{{ old('titulo', $pelicula->titulo) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            @error('titulo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Año -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="anio">
                                Año
                            </label>
                            <input type="number" 
                                   name="anio" 
                                   id="anio" 
                                   value="{{ old('anio', $pelicula->anio) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('anio')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duración -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="duracion">
                                Duración (minutos)
                            </label>
                            <input type="number" 
                                   name="duracion" 
                                   id="duracion" 
                                   value="{{ old('duracion', $pelicula->duracion) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('duracion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sinopsis -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="sinopsis">
                                Sinopsis
                            </label>
                            <textarea name="sinopsis" 
                                      id="sinopsis" 
                                      rows="5"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('sinopsis', $pelicula->sinopsis) }}</textarea>
                            @error('sinopsis')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Poster Path -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="poster_path">
                                URL del póster
                            </label>
                            <input type="text" 
                                   name="poster_path" 
                                   id="poster_path" 
                                   value="{{ old('poster_path', $pelicula->poster_path) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Ej: https://image.tmdb.org/t/p/w500/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg</p>
                            @error('poster_path')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Actualizar Película
                            </button>
                            <a href="{{ route('peliculas.show', $pelicula) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>