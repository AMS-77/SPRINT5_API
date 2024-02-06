<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayersRankingTest extends TestCase
{
    use RefreshDatabase;
    public function testPlayersRanking()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        Role::create(['name' => 'admin']);
        // Creamos un usuario con el rol 'admin'.
        $admin = User::factory()->create();  
        $admin->assignRole('admin');
        $this->actingAs($admin);

        // Petición GET para obtener el ranking.
        $response = $this->get("/api/players/ranking");

        // Comprueba que la respuesta sea 200 (OK)    
        $this->assertEquals(200, $response->getStatusCode());
        
        //Aseguramos que nos llega la media de exito de todos los jugadores
        $responseDB = $response->json();
        $this->assertArrayHasKey('media success', $responseDB);
    }
}
