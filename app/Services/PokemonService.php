<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PokemonService
{
    private $apiUrl = 'https://pokeapi.co/api/v2/';

    /**
     * Extrae el ID del Pokémon desde la URL proporcionada por la API.
     * @param mixed $url
     * @return string
     */
    private function extractIdFromUrl($url)
    {
        $parts = explode('/', rtrim($url, '/'));
        return end($parts);
    }

    /**
     * Obtiene una lista de Pokémon con paginación.
     * @param mixed $limit
     * @param mixed $offset
     */
    public function getPokemons($offset = 0, $limit = 20)
    {
        return Cache::remember("pokemon_list_{$offset}_{$limit}", 3600, function () use ($limit, $offset) {
            try {
                $response = Http::get($this->apiUrl . 'pokemon', [
                    'offset' => $offset,
                    'limit' => $limit,
                ]);

                if (!$response->successful()) {
                    throw new NotFoundException('Failed to fetch pokemons from API');
                }

                $data = $response->json();

                $pokemons = array_map(function ($pokemon) {
                    $id = $this->extractIdFromUrl($pokemon['url']);
                    return [
                        'id' => $id,
                        'name' => $pokemon['name'],
                        'image' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png", // Imagen desde otra fuente para optimizar carga
                        'url' => $pokemon['url']
                    ];
                }, $data['results']);

                return $pokemons;

            } catch (NotFoundException $e) {
                Log::warning('Failed to fetch pokemon list', [
                    'message' => $e->getMessage(),
                    'offset' => $offset,
                    'limit' => $limit,
                ]);
                return [];
            } catch (\Exception $e) {
                Log::error('Unexpected error fetching pokemons', [
                    'message' => $e->getMessage(),
                    'offset' => $offset,
                    'limit' => $limit,
                    'trace' => $e->getTraceAsString()
                ]);
                return [];
            }
        });
    }

    /**
     * Obtiene los detalles de un Pokémon por nombre o ID.
     * @param mixed $nameOrId
     * @return array|null
     */
    public function getPokemon($nameOrId)
    {
        return Cache::remember("pokemon_{$nameOrId}", 3600, function () use ($nameOrId) {
            try {
                $response = Http::get($this->apiUrl . "pokemon/" . strtolower($nameOrId));

                if (!$response->successful()) {
                    throw new NotFoundException("Pokemon '{$nameOrId}' not found");
                }

                $data = $response->json();
                return [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'image' => $data['sprites']['front_default'],
                    'image_hd' => $data['sprites']['other']['official-artwork']['front_default'] ?? $data['sprites']['front_default'],
                    'types' => collect($data['types'])->map(function ($typeData) {
                        return [
                            'name' => $typeData['type']['name'],
                            'slot' => $typeData['slot']
                        ];
                    })->toArray(),

                    'stats' => collect($data['stats'])->map(function ($statData) {
                        return [
                            'name' => $statData['stat']['name'],
                            'value' => $statData['base_stat']
                        ];
                    })->toArray(),
                    'height' => $data['height'] / 10, // Altura en metros
                    'weight' => $data['weight'] / 10, // Peso en kg
                    'number' => str_pad($data['id'], 3, '0', STR_PAD_LEFT),
                ];

            } catch (NotFoundException $e) {
                Log::warning('Pokemon not found', ['nameOrId' => $nameOrId]);
                return null;
            } catch (\Exception $e) {
                Log::error('Unexpected error', [
                    'message' => $e->getMessage(),
                    'nameOrId' => $nameOrId,
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }
        });
    }

    /**
     * Obtiene los detalles de un Pokémon por nombre o ID. Utilizado para búsqueda.
     * @param mixed $nameOrId
     * @return array|null
     */
    public function searchPokemon($query)
    {
        return $this->getPokemon($query);
    }

    /**
     * Busca Pokémon por nombre parcial.
     * @param mixed $query
     * @param mixed $limit
     * @return array
     */
    public function searchByPartialName($query, $limit = 151)
    {
        try {

            $allPokemon = $this->getPokemons(0, $limit);
            if (empty($allPokemon)) {
                throw new NotFoundException('No pokemons available from API');
            }

            $query = strtolower(trim($query));

            $filtered = array_filter($allPokemon, function($pokemon) use ($query) {
                return strpos(strtolower($pokemon['name']), $query) !== false;
            });

            $results = array_map(function($pokemon) {
                $id = $this->extractIdFromUrl($pokemon['url']);
                return [
                    'id' => $id,
                    'name' => $pokemon['name'],
                    'image' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$id}.png",
                ];
            }, \array_slice($filtered, 0, $limit));

            return array_values($results);

        } catch (NotFoundException $e) {
            Log::warning('Pokemon partial search failed - API unavailable', [
                'message' => $e->getMessage(),
                'query' => $query,
                'limit' => $limit,
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('Unexpected error during partial search', [
                'message' => $e->getMessage(),
                'query' => $query,
                'limit' => $limit,
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
