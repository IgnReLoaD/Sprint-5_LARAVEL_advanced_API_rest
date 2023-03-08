<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;

// TDD Exercice-4 - USER-EDIT
class EditPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function a_player_can_edit_name()
    {
        $this->withoutExceptionHandling();
        $this->artisan('passport:install');

        $objUser = User::factory()->create();
        Passport::ActingAs($objUser);
        // $this->put('api/players/{id}', [
        // $this->put('api/players/{$objUser->id_user} , [])
        $this->put('api/players/' . $objUser->id, [
            'name' => 'newName'
        ]);

        // comprobar que en la BD se haya modificado
        $this->assertDatabaseHas('users',[
            'name' => 'newName'
        ]);
            
    }
}

?>
