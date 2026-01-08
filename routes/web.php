<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

// Ruta principal - Listado de Pokémon
Route::get('/', [PokemonController::class, 'index'])->name('pokemons.index');

// Búsqueda de Pokémon
Route::get('/search', [PokemonController::class, 'search'])->name('pokemons.search');

// Detalle de un Pokémon específico
Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'show'])->name('pokemons.show');

