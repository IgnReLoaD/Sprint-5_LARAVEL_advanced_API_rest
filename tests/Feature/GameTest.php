<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Game;

class GameTest extends TestCase
{
    // usamos un Trait (clase que agrupa funciones) se ejecuta cada vez después de ejecutar un Test, en este caso este sirve para Refresh
    use RefreshDatabase;

    // TDD Exercice-5 GAME-PLAY 
    public function logged_user_can_play_a_game() 
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling 
        $this->withoutExceptionHandling(); 
        // simular la utilización del Passport 
        $this->artisan('passport:install'); 

        // Create a single App\Models\User instance...
        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser); 

        // mandar a un endPoint una llamada tipo Http POST a la URL 'players/{id_player}' pero NO le pasamos campos (sin Body del Postman)
        $this->post('api/players/'. $objUser->id . '/games');

        // para asertar que ha funcionado el Test, comprobar haya grabado en Tabla Games en Campo user_id con valor ID del Logged-in User
        $this->assertDatabaseHas('games',[ 
            'user_id' => $objUser->id 
        ]); 
    }

    // TDD Exercice-6 GAME-SHOW-PLAYER-SCORE
    public function logged_user_can_show_his_games() 
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling 
        $this->withoutExceptionHandling(); 
        // simular la utilización del Passport 
        $this->artisan('passport:install'); 
        
        // Create a single App\Models\User instance... 
        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser); 

        // mandar a un endPoint una llamada tipo Http GET pero esta vez Invocando a la ruta ya creada pasándole como Param el ID
        $response = $this->actingAs($objUser, 'api')->get(route('showPlayerScore',$objUser->id));
        $response->assertStatus(200);
    }

    // TDD Exercice-7 GAME-DELETE-PLAYER-SCORE
    public function logged_user_can_delete_his_games()
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling
        $this->withoutExceptionHandling();
        // simular la utilización del Passport
        $this->artisan('passport:install');

        // Create a single App\Models\User instance...
        $objUser = User::factory()->create();
        Passport::actingAs($objUser);

        // mandar a un endPoint una llamada tipo Http DELETE invocando a la ruta ya creada pasándole como Param el ID
        $response = $this->actingAs($objUser, 'api')->delete(route('delPlayerScore',$objUser->id));
        $response->assertStatus(200);
    }


}
