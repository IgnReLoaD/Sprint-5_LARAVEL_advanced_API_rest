<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\GameController;

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


// Has de tindre en compte els següents detalls de construcció:
// URL’s:

// POST /players : crea un jugador/a.
// postman: nom, email, contrasenya, confirm-contrasenya
Route::post('/players', [UserController::class, 'register'])->name('register');
// postman: email, contrasenya
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api'])->group(function () {
    // PUT /players/{id} : modifica el nom del jugador/a.
    Route::put('/players/{id}', [UserController::class, 'edit'])->name('edit');

    // POST /players/{id}/games/ : un jugador/a específic realitza una tirada dels daus.
    Route::post('/players/{id}/games', [GameController::class, 'play'])->name('play');

    // DELETE /players/{id}/games: elimina les tirades del jugador/a.
    Route::delete('/players/{id}/games', [GameController::class, 'destroyUserScore'])->name('destroyUserScore'); 

    // GET /players: retorna el llistat de tots els jugadors/es del sistema amb el seu percentatge mitjà d’èxits 
    Route::get('/players', [GameController::class, 'allPlayersScore'])->name('allPlayersScore');

    // GET /players/{id}/games: retorna el llistat de jugades per un jugador/a.
    Route::get('/players/{id}/games', [GameController::class, 'showUserScore'])->name('showUserScore');

    // GET /players/ranking: retorna el rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
    Route::get('/players/ranking', [GameController::class, 'ranking'])->name('ranking'); 

    // GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
    Route::get('/players/ranking/loser', [GameController::class, 'loser'])->name('loser');

    // GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.
    Route::get('/players/ranking/winner', [GameController::class, 'winner'])->name('winner');
});
