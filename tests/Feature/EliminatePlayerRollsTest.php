<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EliminatePlayerRollsTest extends TestCase
{
    use RefreshDatabase;
    public function testEliminatePlayerRolls()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');
        
            // Crea los roles
        Role::create(['name' => 'player']);
        Role::create(['name' => 'admin']);

        // Crea un usuario y autentícalo
        $user = User::factory()->create();  
        $user->assignRole('player');
        $this->actingAs($user);           

        // Creamos algunos juegos de prueba para este usuario
        Game::factory()->count(3)->create(['user_id' => $user->id]);

        // Petición DELETE para eliminar los juegos del usuario
        $response = $this->delete("/api/players/{$user->id}/games");

        // Comprueba que la respuesta sea 200 (OK)    
        $this->assertEquals(200, $response->getStatusCode());

        // Si el numero de juegos del usuario es 0...
        $this->assertCount(0, Game::where('user_id', $user->id)->get());
    }
}
