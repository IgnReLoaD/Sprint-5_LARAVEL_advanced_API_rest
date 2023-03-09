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

// TDD Exercice-8 GAME-SHOW-ALL-PLAYERS-SCORE
class GameGetAllPlayersScoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function logged_user_can_see_all_players_score()
    {
        $this->withoutExceptionHandling();
        $this->artisan('passport:install');

        // Create a single App\Models\User instance...
        $objUser = User::factory()->create(); 
        Passport::actingAs($objUser);

        // mandar a un endPoint una llamada tipo Http GET invocando a la ruta ya creada pasándole como Param el ID
        $response = $this->actingAs($objUser,'api')->get(route('allPlayersScore',$objUser->id));
        $response->assertStatus(200);
    }
}

?>
