<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function play($id_player)
    {
        // return response([
        //     'message' => 'entra en GameController::play'
        // ]);
        // die;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
}
