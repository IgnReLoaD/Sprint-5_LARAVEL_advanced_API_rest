<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'user_id', 'intResult1', 'intResult2', 'boolWinner'];

    // GAMES N--1 USER-PLAYER (Left Join) ...belongsTo()
    public function player(){
        // return $this->belongsTo(User::class,'id');
        return $this->belongsTo('App\Models\User'); 
    }    
}
