<?php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        // Crear el cliente de acceso personalizado para la BD virtual
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');
        
        // Crear un usuario de prueba
        User::factory()->create([
            'email' => 'email@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Intentar iniciar sesión con el usuario creado
        //Simula solicitud POST a la ruta /api/login
            $response = $this->json('POST', '/api/login', [   
            'email' => 'email@example.com',
            'password' => 'password',]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
        /*aseguramos de que la respuesta JSON devuelta por el endpoint /api/login 
        contiene el token de acceso, es una forma más directa de comprobarlo
        aunque assertJsonStructure tambien verifique que contenga el token*/
        $this->assertArrayHasKey('token', $response->json());
    }
}