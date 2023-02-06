<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Has de tindre en compte els següents detalls de construcció:
// URL’s:

// POST /players : crea un jugador/a.
Route::post('/players', [UserRegistered::class, 'register'])->name('register');

// PUT /players/{id} : modifica el nom del jugador/a.
// POST /players/{id}/games/ : un jugador/a específic realitza una tirada dels daus.
// DELETE /players/{id}/games: elimina les tirades del jugador/a.
// GET /players: retorna el llistat de tots els jugadors/es del sistema amb el seu percentatge mitjà d’èxits 
// GET /players/{id}/games: retorna el llistat de jugades per un jugador/a.
// GET /players/ranking: retorna el rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
// GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
// GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.