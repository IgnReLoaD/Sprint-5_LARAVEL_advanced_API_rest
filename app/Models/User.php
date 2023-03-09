<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'sysadmin'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // USER-PLAYER 1--N GAMES (Right Join) ... hasMany()
    public function games(){
        // return $this->hasMany(Game::class, 'id');
        return $this->hasMany('App\Models\Game');
    } 	

    // USER - Porcentaje de Exito (ganados/intentos) % ... de donde intentos no puede ser 0,
    // una primera función para encapsular la detección de las partidas del usuario actual
    public function getUserGames()
    {
        return Game::where('user_id', $this->id)->get();
    }    
    // una segunda función para Porcentaje Éxito invocando dos veces la Func anterior (totales y ganados)
    public function userSuccess()
    {
        $gamesPlayed = $this->getUserGames()->count();
        $gamesHasWon = $this->getUserGames()->where('boolWinner',1)->count();
        // IF ternario para controlar División por 0 y devolver el Cálculo con 2 decimales
        return ($gamesPlayed > 0) ? round($gamesHasWon / $gamesPlayed * 100, 2) : 0;
    }
    
}
