<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;


Route::get('/', [PokemonController::class, 'index'])->name('pokemons.index');
Route::get('/search', [PokemonController::class, 'search'])->name('pokemons.search');
Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'show'])->name('pokemons.show');

