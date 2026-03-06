<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Película') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Mostrar errores de validación --}}
                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORMULARIO DE CREACIÓN --}}
                    <form method="POST" action="{{ route('peliculas.store') }}" class="space-y-6">
                        @csrf {{-- Token de seguridad obligatorio --}}

                        {{-- Título --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                            <input type="text" 
                                   name="titulo" 
                                   value="{{ old('titulo') }}" 
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Campo obligatorio</p>
                        </div>

                        {{-- Año --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                            <input type="number" 
                                   name="anio" 
                                   value="{{ old('anio') }}" 
                                   min="1900" 
                                   max="{{ date('Y') + 1 }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Ej: 2024</p>
                        </div>

                        {{-- Duración --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duración (minutos)</label>
                            <input type="number" 
                                   name="duracion" 
                                   value="{{ old('duracion') }}" 
                                   min="1"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Ej: 120</p>
                        </div>

                        {{-- Sinopsis --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sinopsis</label>
                            <textarea name="sinopsis" 
                                      rows="5"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('sinopsis') }}</textarea>
                        </div>

                        {{-- URL del póster --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL del póster</label>
                            <input type="url" 
                                   name="poster_path" 
                                   value="{{ old('poster_path') }}"
                                   placeholder="https://ejemplo.com/imagen.jpg"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">URL completa de la imagen (opcional)</p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-4 pt-4">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                Guardar Película
                            </button>
                            <a href="{{ route('peliculas.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>