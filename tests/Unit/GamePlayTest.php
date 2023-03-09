<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;
use App\Models\Game;

// TDD Exercice-5 GAME-PLAY 
class GamePlayTest extends TestCase
{
    // usamos un Trait (clase que agrupa funciones) se ejecuta cada vez después de ejecutar un Test, en este caso este sirve para Refresh
    use RefreshDatabase;
    /**
     * @test
     */
    public function logged_user_can_play_a_game() 
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('passport:install'); 

        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser); 

        $this->post('api/players/'. $objUser->id . '/games');

        // para asertar que ha funcionado el Test, comprobar haya grabado en Tabla Games en Campo user_id con valor ID del Logged-in User
        $this->assertDatabaseHas('games',[ 
            'user_id' => $objUser->id 
        ]); 
    }
}

?>
