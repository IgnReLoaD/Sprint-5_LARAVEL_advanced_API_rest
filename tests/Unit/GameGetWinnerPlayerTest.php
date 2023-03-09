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

// TDD Exercice-11 GAME-SHOW-WINNER-PLAYER
class GameGetWinnerPlayerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function logged_user_can_get_winner_player()
    {
        $this->withoutExceptionHandling();
        $this->artisan('passport:install');

        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser); 

        // mandar a un endPoint una llamada tipo Http GET invocando a la ruta ya creada pasándole como Param el ID
        $response = $this->actingAs($objUser,'api')->get(route('winnerPlayer',$objUser->id));
        $response->assertStatus(200);
    }
}

?>
