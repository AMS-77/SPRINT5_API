<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPlaysPlayerTest extends TestCase
{
    use RefreshDatabase;
    public function testListPlaysPlayer()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        Role::create(['name' => 'player']);
        Role::create(['name' => 'admin']);

        // Crea un usuario con el rol 'player'
        $user = User::factory()->create();  
        $user->assignRole('player');
        $this->actingAs($user);

        // Creamos 3 tiradas para este usuario
        Game::factory()->count(3)->create(['user_id' => $user->id]);

        // Realiza una petición GET para obtener los juegos del usuario
        $response = $this->get("/api/players/{$user->id}/games");

        // Comprueba que la respuesta sea 200 (OK)    
        $this->assertEquals(200, $response->getStatusCode());

        // Comprueba que la respuesta contenga 3 juegos.
        $responseDB = $response->json();
        $this->assertCount(3, $responseDB['games']);

        // Ahora, probaremos con el usuario con rol admin (No autorizado)
        $admin = User::factory()->create();  
        $admin->assignRole('admin');
        $this->actingAs($admin);

        // Intenta realizar la misma petición GET
        $response = $this->get("/api/players/{$admin->id}/games");

        // La respuesta debe ser 403 (no autorizado)
        $this->assertEquals(403, $response->getStatusCode());
    }
}
