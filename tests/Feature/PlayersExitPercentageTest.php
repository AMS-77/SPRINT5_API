<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayersExitPercentageTest extends TestCase
{
    use RefreshDatabase;
    public function testPlayersExitPercentage()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        // Crea los roles
        Role::create(['name' => 'player']);
        Role::create(['name' => 'admin']);

        // Creamos un usuario con el rol 'admin'
        $admin = User::factory()->create();  
        $admin->assignRole('admin');
        $this->actingAs($admin);

        //Petición GET para obtener el ranking de jugadores
        $response = $this->get("/api/players");

        // Comprueba que la respuesta es 200 (OK)    
        $this->assertEquals(200, $response->getStatusCode());

        // Ahora, probamos con un usuario tipo 'player'
        $player = User::factory()->create();  
        $player->assignRole('player');
        $this->actingAs($player);

        // Intenta realizar la misma petición GET
        $response = $this->get("/api/players");

        // Comprueba que la respuesta sea 403 (no Autorizado)
        $this->assertEquals(403, $response->getStatusCode());
    }
}
