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

// TDD Exercice-7 GAME-DELETE-PLAYER-SCORE
class GameDelPlayerScoreTest extends TestCase
{
    use RefreshDatabase;

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

?>