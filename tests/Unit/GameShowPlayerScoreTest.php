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

// TDD Exercice-6 GAME-SHOW-PLAYER-SCORE
class GameShowPlayerScoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function logged_user_can_show_his_games() 
    {
        $this->withoutExceptionHandling(); 
        $this->artisan('passport:install'); 
        
        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser); 

        // mandar a un endPoint una llamada tipo Http GET pero esta vez Invocando a la ruta ya creada pasándole como Param el ID
        $response = $this->actingAs($objUser, 'api')->get(route('showPlayerScore',$objUser->id));
        $response->assertStatus(200);
    }
}

?>