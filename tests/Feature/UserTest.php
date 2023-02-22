<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use App\Models\User;
// use App\Models\Game;

class UserTest extends TestCase
{
    // usamos un Trait (clase que agrupa funciones) se ejecuta cada vez después de ejecutar un Test, en este caso este sirve para Refresh
    use RefreshDatabase;

    // TDD Exercice-1 - USER-REGISTER
    public function a_player_can_be_registered()
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling
        $this->withoutExceptionHandling();
        // simular la utilización del Passport
        $this->artisan('passport:install');

        // mandar a un endPoint una llamada tipo Http POST a la URL 'players' i le pasamos los campos (los del Body del Postman)
        $response = $this->postJson('api/players', [
            'name' => 'userNameTest',
            'email' => 'test@test.com',
            'password' => 'pwd',
            'password_confirmation' => 'pwd',
            'sysadmin' => '1'
        ]);
        // una confirmación de que la función se ha ejecutado correctamente
        $response->assertCreated();
        // comprobar que haya almacenado algo, así que contamos registros y vemos si hay mínimo un registro insertado
        $this->assertCount(1, User::all());
        // recuperamos este primer registro 
        $objUser = User::first();
        // para comparar sus valores grabados así assertamos que haya funcionado el Test
        $this->assertEquals( $objUser->name, 'userNameTest');
        $this->assertEquals( $objUser->email, 'test@test.com');
    }

    // TDD Exercice-2 - USER-LOGIN
    public function a_player_can_be_logged_in()
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling
        $this->withoutExceptionHandling();
        // simular la utilización del Passport
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

    // TDD Exercice-3 - USER-LOGOUT
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

    // TDD Exercice-4 - USER-EDIT
    public function a_player_can_edit_name()
    {
        // desactivar el manejador de excepciones porque sino el Test no me diría si/no, ya que lo captura el Catch del ExceptionHandling
        $this->withoutExceptionHandling();
        // simular la utilización del Passport
        $this->artisan('passport:install');

        // Create a single App\Models\User instance...
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
