<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Http\Request;


class playerRollsDiceTest extends TestCase
{
    use RefreshDatabase;
    public function testPlayerRollsDice()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');
        
            // Creamos los roles que probaremos
        Role::create(['name' => 'player']);
        Role::create(['name' => 'admin']);

        //Creamos usuario con rol player    
        $user = User::factory()->create();  
        $user->assignRole('player');
        $this->actingAs($user);
        
        $response = $this->post("/api/players/{$user->id}/games", ['dice1' => 3, 'dice2' => 4]);
        // Comprueba que la respuesta sea 201 (OK)    
        $this->assertEquals(201, $response->getStatusCode());

        // Probamos un usuario con el rol 'admin' (no tiene permisos y debe salir mal)
        $user = User::factory()->create();  
        $user->assignRole('admin');
        $this->actingAs($user);
        $user = $user->refresh();   
        // Intentamos hacer la misma petición POST
        $response = $this->post("/api/players/{$user->id}/games", ['dice1' => 3, 'dice2' => 4]);

        // Comprueba que la respuesta sea 403 (no autorizado)
        $this->assertEquals(403, $response->getStatusCode());
    }

}
