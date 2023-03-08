<?php

namespace Tests\Unit;
use PHPUnit\Framework\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePlayerTest extends TestCase
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
}

?>