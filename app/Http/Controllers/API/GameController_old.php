<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Laravel\Passport\HasApiTokens;
use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    // Exercice-5 ... POST /players/{id}/games --> un jugador/a específic realitza una tirada dels daus.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (i graba en MySql tabla Games)
    public function play($id_player)
    {
        // return response([
        //     'message' => 'entra en GameController::play'
        // ]);
        // die;

        $idUserLoggedIn = Auth::user()->id;

        if ($idUserLoggedIn == $id_player) { 
            // ENUNCIAT:   En cas que la suma del resultat dels dos daus sigui 7, la partida és guanyada, si no és perduda. 
            $objGame = new Game();
            $objGame->user_id = $id_player;
            $objGame->intResult1 = rand(1, 6);
            $objGame->intResult2 = rand(1, 6);
            $objGame->boolWinner = (($objGame->intResult1 + $objGame->intResult2) == 7);
            $objGame->save();

            return response()->json([ 
                'Dice 1 = ' => $objGame->intResult1, 
                'Dice 2 = ' => $objGame->intResult2, 
                'Sum two dices = ' => $objGame->intResult1 + $objGame->intResult2, 
                'Result: ' => ($objGame->boolWinner) ? 'won' : 'lost', 
            ]); 

        }else{
            return response([
                'advise' => 'User ' . $id_player . ' not found, or at least not logged in.',
                'clue' => 'The currently logged user is the number ' . $idUserLoggedIn
            ]);
        }
    }

    // Exercice-6 ... GET /players/{id}/games --> retorna el llistat de jugades per un jugador/a.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (si OK retorna average d'encert i les tirades).
    public function showPlayerScore($id_player)
    {
        $idUserLoggedIn = Auth::id();
        if ($idUserLoggedIn != $id_player) { 
            return response([
                'advise' => 'User ' . $id_player . ' not found, or at least not logged in.',
                'clue' => 'User ' .$idUserLoggedIn. ' is the real who is logged in right now.'
            ]);
        }else{
            $objCurrentUsr = User::find($id_player);
            $gamesByPlayer = Game::where('user_id', $id_player)->get();
            if ($gamesByPlayer->isEmpty()) {
                return response([
                    'message' => 'This player has not games yet.'
                ]);            
            }
            $countAllGames = $gamesByPlayer->count();
            $countWonGames = $gamesByPlayer->where('boolWinner', true)->count();
            $successAverage = ($countWonGames / $gamesByPlayer->count()) * 100;
            return response([
                'current user logged' => $objCurrentUsr->name,
                'game attempts' => $countAllGames,
                'success average' => $successAverage . '%',
                'game list' => $gamesByPlayer
            ]);
        }
    }

    // Exercice-7 ... DELETE /players/{id}/games --> elimina les tirades del jugador/a loguejat.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (si OK graba en MySql tabla Games - borra)
    public function delPlayerScore($id_player)
    {
        // return response([
        //     'message' => 'entra en GameController::delPlayerScore'
        // ]);
        // die;

        $idUserLoggedIn = Auth::id();
        $objUser = User::find($id_player);

        if (!$objUser) {
            return response([
                'message' => 'User not found, could not delete their scores!',
                'status' => 404,
            ]);
        } elseif($idUserLoggedIn == $id_player) {
            $userGames = Game::where('user_id', '=', $id_player)->first('id');
            if($userGames !== null) {
                Game::where('user_id', $id_player)->delete();
                return response(['message' => 'Games of player number ' . $objUser->id . ", called " . $objUser->name . ', sucessfully deleted.']);    
            } else {
                return response(['message' => "The player number " . $objUser->id . ", called " . $objUser->name . ", don't have any game!"]);
            }
        }
    }

    // Exercice-8 ... GET /players --> retorna el llistat de tots els jugadors del sistema amb el percentatge mitjà d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok (avisant si l'usuari loguejat és Admin o no)
    public function allPlayersScore()
    {

        $idUserLoggedIn = Auth::id();
        $objUser = User::find($idUserLoggedIn);

        if (!$objUser->sysadmin){
            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", is not SysAdmin. So he/she cannot see the info related to other players."
            ]);
        }else{
            $allUsers = User::all();
            $arrUsers = [];

            foreach ($allUsers as $user) {
                // total partides jugades i total de guanyades
                $userGames = Game::where('user_id',$user->id)->get();
                $countGames = $userGames->count();
                $countWonGames = $userGames->where('boolWinner',true)->count();

                // percentatge d'encert
                $successAverage = 0;
                if ($countGames > 0) {
                    $successAverage = ($countWonGames / $countGames) * 100;
                }

                // preparar dades d'un jugador
                $userDetails = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'sysadmin' => $user->sysadmin ? true : false,
                    'attempts' => $countGames,
                    'success' => $countWonGames,
                    'average' => $successAverage.'%'
                ];

                // afegir al llistat
                array_push($arrUsers, $userDetails);
            }

            // sortida de dades
            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", and is SysAdmin. So he/she can see the following info related to other players.",
                'players' => $arrUsers,
                'status'  => 200
            ]);
        }
    }

    // Exercice-9 ... GET /players/ranking: retorna el rànquing mitjà de tots els jugadors del sistema. És a dir, el percentatge mitjà d’èxits.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Llistat Ranking (avisa si l'usuari loguejat és Admin o no).
    public function allPlayersRanking(){
        // return response([
        //     'message' => 'entra en GameController::allPlayersRanking'
        // ]);
        // die;

        $idUserLoggedIn = Auth::id();
        $objUser = User::find($idUserLoggedIn);

        if (!$objUser->sysadmin){
            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", is not SysAdmin. So he/she cannot see the info related to other players."
            ]);
        }else{
            $fieldsetAttempts = DB::select("SELECT COUNT(*) as fieldAttempts FROM games");
            $fieldsetSuccess = DB::select("SELECT COUNT(*) as fieldSuccess FROM games WHERE boolwinner='1'");
            if ($fieldsetAttempts[0]->fieldAttempts != 0){
                $average = $fieldsetSuccess[0]->fieldSuccess / $fieldsetAttempts[0]->fieldAttempts * 100;
                return response()->json([
                    'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", and is SysAdmin. So can see the following info related to other players.",
                    'attempts all players' => $fieldsetAttempts[0]->fieldAttempts,
                    'success all players' => $fieldsetSuccess[0]->fieldSuccess,
                    'Avg ranking all players' => round($average,4) . ' %',
                    'status'  => 200
                ]);            
            }else{
                return response()->json([
                    'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", and is SysAdmin. So can see the following info related to other players.",
                    'attempts all players' => "You need to play minimum a game to retrieve any ranking!",
                    'success all players' => "You need to play minimum a game to retrieve any ranking!",
                    'Avg ranking all players' => round($average,4) . ' %',
                    'status'  => 200
                ]);  
            }

        }
    }

    // Exercice-10... GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Loser (avisa si l'usuari loguejat és Admin o no).
    public function loserPlayer(){
        $idUserLoggedIn = Auth::id();
        $objUser = User::find($idUserLoggedIn);

        if (!$objUser->sysadmin){
            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", is not SysAdmin. So he/she cannot see the info related to other players."
            ]);
        }else{
            $resultLoserPlayer = DB::select("
                SELECT user_id, COUNT(*) as attempts_success FROM games
                WHERE boolWinner=0
                GROUP BY user_id 
                ORDER BY attempts_success DESC
                LIMIT 1
            ");
            $objLoserPlayer = User::find($resultLoserPlayer[0]->user_id);

            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", and is SysAdmin. So can see the following info related to other players.",                
                'loser' => "The bad player is number " . $objLoserPlayer->id . ", called " . $objLoserPlayer->name . "!!",
                'status'  => 200
            ]);
        }
    }

    // Exercice-11... GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok amb el Winner (avisa si l'usuari loguejat és Admin o no). 
    public function winnerPlayer(){
        $idUserLoggedIn = Auth::id();
        $objUser = User::find($idUserLoggedIn);

        if (!$objUser->sysadmin){
            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", is not SysAdmin. So he/she cannot see the info related to other players."
            ]);
        }else{
            $resultWinnerPlayer = DB::select("
                SELECT user_id, COUNT(*) as attempts_success FROM games
                WHERE boolWinner=1
                GROUP BY user_id
                ORDER BY attempts_success DESC
                LIMIT 1
            ");
            $objWinnerPlayer = User::find($resultWinnerPlayer[0]->user_id);

            return response()->json([
                'message' => "Current logged user is " . $idUserLoggedIn . ", called " . $objUser->name . ", and is SysAdmin. So can see the following info related to other players.",                
                'winner' => "The best player is number " . $objWinnerPlayer->id . ", called " . $objWinnerPlayer->name . "!!",
                'status'  => 200
            ]);            
        }
    }


}
