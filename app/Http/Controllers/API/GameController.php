<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Postman:  POST /players/{id}/games 
     * @param  int  $id_player
     * @return \Illuminate\Http\Response --> message
     */
    public function play($id_player)
    {
        return response([
            'message' => 'entra en GameController::play'
        ]);
        die;

        $authUser = Auth::user()->id;

        if ($authUser == $id_player) { 
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
                'message' => 'user not found'
            ]);
        }
    }


     /**
     * Entrar en Postman en BODY .
     * @param  int  $id_user
     * @return \Illuminate\Http\Response  --> message
     */

    public function destroyUserScore($id_user)
    {
        return response([
            'message' => 'entra en GameController::destroyUserScore'
        ]);
        die;

        $authUser = Auth::id();
        $user = User::find($id_user);

        if (!$user) {
            return response([
                'message' => 'User not found, could not delete their scores!',
                'status' => 404,
            ]);
        } elseif($authUser == $id) {
            $userGames = Game::where('user_id', '=', $id)->first('id');

            if($userGames !== null) {
                Game::where('user_id', $id)->delete();
                return response(['message' => 'Games deleted from user ' . $user->name]);            
            } else {
                return response(['message' => "The user " . $user->name . " don't have any game!"]);
            }
        }
    }

    public function allPlayersScore()
    {
        $users = User::all();
        $arrUsers = [];

        foreach ($arrUsers as $user) {
            // total partides jugades i total de guanyades
            $userGames = Game::where('user_id',$user->id)->get();
            $countGames = $userGames->count();
            $countWonGames = $userGames->where('boolWinner',true)->count();

            // percentatge d'encert
            $successAverage = 0;
            if ($countGames > 0) {
                $successAverage = ($countWonGames / $countGames) * 100;
            }

            // preparar dades
            $userDetails = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'average' => $successAverage
            ];
            array_push($arrUsers, $userDetails);
        }

        // sortida de dades
        return response()->json([
            'players' => $arrUsers,
            'status'  => 200
        ]);
    }

    public function showPlayerScore($id_player)
    {
        $authUser = Auth::id();
        $gamesByPlayer = Game::where('user_id', $id_player)->get();

        if ($gamesByPlayer->isEmpty()) {
            return response([
                'message' => 'This player has not games yet'
            ]);            
        }

        $countWonGames = $gamesByPlayer->where('boolWinner', true)->count();
        $successAverage = ($countWonGames / $gamesByPlayer->count()) * 100;

        return response([
            'games' => $gamesByPlayer,
            'success' => $successAverage . '%'
        ]);
    }

}
