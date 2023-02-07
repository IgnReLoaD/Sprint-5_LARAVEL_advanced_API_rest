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
    public function index()
    {
        // IF ($objUser->id == ADMIN)
        $recordsetGames = Game::all();          
        // ELSE 
        //      $recordsetGames = Game::select("*")->where('user_id','=',$objUser->id)->get()->sortByDesc('name');
        // END IF
        
        return view('game.index')->with('recordsetGames',$recordsetGames);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ENUNCIAT:   En cas que la suma del resultat dels dos daus sigui 7, la partida és guanyada, si no és perduda. 

        $objGame = new Game();
        // després quan fem l'usuari podrem grabar en user_id el valor del seu ID
        $objGame->user_id = '1';
        $objGame->intResult1 = rand(1, 6);
        $objGame->intResult2 = rand(1, 6);
        $objGame->boolWinner = (($objGame->intResult1 + $objGame->intResult2) == 7);
        $objGame->save();
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
    public function destroy($id)
    {
        //
    }
}
