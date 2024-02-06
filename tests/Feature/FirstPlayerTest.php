<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FirstPlayerTest extends TestCase
{
    use RefreshDatabase;
    public function testFirstPlayer()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();  
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $response = $this->get("/api/players/ranking/winner");
        // Comprueba que la respuesta sea 200 (OK)    
        $this->assertEquals(200, $response->getStatusCode());
    }
}
