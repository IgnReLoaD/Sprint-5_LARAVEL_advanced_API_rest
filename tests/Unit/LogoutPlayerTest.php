<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;

// TDD Exercice-3 - USER-LOGOUT
class LogoutPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function a_player_can_be_logged_out()
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling
        $this->withoutExceptionHandling();
        // simular la utilización del Passport
        $this->artisan('passport:install');

        // Create a single App\Models\User instance...
        $objUser = User::factory()->create();
        Passport::actingAs($objUser);
        $response = $this->postJson('api/logout', []);
        $response->assertStatus(200);
    }
}

?>
