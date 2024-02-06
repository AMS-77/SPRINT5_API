<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    public function testLogout()
    {
        // Crear el cliente de acceso personalizado para la BD virtual
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        //Creamos el usuario
        $user = User::factory()->create();

        //Creamos un nuevo token de acceso
        $token = $user->createToken('TestToken')->accessToken;
        
        // Hacer la petición POST a 'api/logout'
        $response = $this->withToken($token)->post('api/logout');
        
        // Si nos devuelve 200 quiere decir que se cerró la sesión
        $response->assertStatus(200);  
    }
}