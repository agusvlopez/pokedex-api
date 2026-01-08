<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pokédex') - Pokédex API</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">

                <a href="{{ route('pokemons.index') }}" class="text-gray-800 text-2xl font-bold">
                    Pokédex
                </a>

                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('pokemons.index') }}" class="text-gray-800 hover:text-gray-200">
                        Todos los Pokémon
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 h-screen">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 Pokédex - <a target="_blank" href="https://pokeapi.co/" class="text-yellow-400">PokéAPI</a></p>
        </div>
    </footer>
</body>
</html>
