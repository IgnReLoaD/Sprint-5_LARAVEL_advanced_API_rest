<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;

// TDD Exercice-2 - USER-LOGIN
class LoginPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function a_player_can_be_logged_in()
    {
        $this->withoutExceptionHandling();
        $this->artisan('passport:install');

        // Create a single App\Models\User instance...
        $objUser = User::factory()->create([
            'email' => 'test@testing.com',
            'password' => bcrypt($encryptedPassword = 'Pwd')
        ]);

        // mandar a un endPoint una llamada tipo Http POST a la URL 'players' i le pasamos los campos (los del Body del Postman)
        $response = $this->postJson('api/login', [
            'email' => $objUser->email,
            'password' => $encryptedPassword
        ]);

        // para assertar que haya funcionado el Test
        $response->assertStatus(200);
        $this->assertAuthenticatedAs($objUser);
    }
}

?>
