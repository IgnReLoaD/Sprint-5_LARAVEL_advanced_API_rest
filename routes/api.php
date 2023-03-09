<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\GameController;

// ENUNCIAT: Has de tindre en compte els següents detalls de construcció, URL’s:

// Exercice-1 ... POST /players --> crea un jugador/a. 
//                @Params postman: nom, email, contrasenya, confirm-contrasenya
//                @Retorna Message Ok/Nok (i graba en MySql tabla Users)
Route::post('/players', [UserController::class, 'register'])->name('register');

// Exercice-2 ... POST /login --> login de jugador/a.
//                @Params postman: email, contrasenya 
//                @Retorna Bearer-Token (no graba res en MySql)
Route::post('/login', [UserController::class, 'login'])->name('login');

// --------------------
// MIDDLEWARE PASSPORT:
// --------------------

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api'])->group(function () {

    // Exercice-3 ... POST /logout --> desloguejar usuari jugador/a.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Exercice-4 ... PUT /players/{id} --> modifica el nom del jugador/a.
    //                @Params postman: name i mail a modificar, i Bearer-Token per demostrar qui és.
    //                @Retorna Message Ok/Nok (i graba en MySql tabla Users)
    Route::put('/players/{id}', [UserController::class, 'edit'])->name('edit');

    // Exercice-5 ... POST /players/{id}/games --> un jugador/a específic realitza una tirada dels daus.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (i graba en MySql tabla Games)
    Route::post('/players/{id}/games',[GameController::class, 'play'])->name('play');

    // Exercice-6 ... GET /players/{id}/games --> retorna el llistat de jugades per un jugador/a.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (si OK retorna average d'encert i les tirades)
    Route::get('/players/{id}/games', [GameController::class, 'showPlayerScore'])->name('showPlayerScore');

    // Exercice-7 ... DELETE /players/{id}/games --> elimina les tirades del jugador/a loguejat.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (si OK graba en MySql tabla Games - borra)
    Route::delete('/players/{id}/games',[GameController::class, 'delPlayerScore'])->name('delPlayerScore'); 

    // ----------------------
    // ADMINISTRADOR DEL JOC:
    // ----------------------

    // Exercice-8 ... GET /players --> retorna el llistat de tots els jugadors del sistema amb el percentatge mitjà d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Llistat (avisant si l'usuari loguejat és Admin o no).
    Route::get('/players',[GameController::class, 'allPlayersScore'])->name('allPlayersScore');

    // Exercice-9 ... GET /players/ranking: retorna el rànquing mitjà de tots els jugadors del sistema. És a dir, el percentatge mitjà d’èxits.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Llistat Ranking (avisa si l'usuari loguejat és Admin o no).
    Route::get('/players/ranking', [GameController::class, 'allPlayersRanking'])->name('allPlayersRanking'); 

    // Exercice-10... GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Loser (avisa si l'usuari loguejat és Admin o no).
    Route::get('/players/loser', [GameController::class, 'loserPlayer'])->name('loserPlayer');

    // Exercice-11... GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Winner (avisa si l'usuari loguejat és Admin o no).    
    Route::get('/players/winner', [GameController::class, 'winnerPlayer'])->name('winnerPlayer');

});

?>
