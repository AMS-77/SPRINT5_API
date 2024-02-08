<?php
namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory as Faker;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUpdateUser()
    {
        // Crear el cliente de acceso personalizado para la BD virtual (Passport)
        (new ClientRepository())->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost');

        $user = User::factory()->create();

        Role::create(['name' => 'player']);
        $user->assignRole('player');

        // Generar un nuevo nombre
        $newName = Faker::create()->name;

        // Datos de actualización
        $updateData = ['name' => $newName,];

        $response = $this->actingAs($user, 'api')
                         ->json('PUT', "/api/players/{$user->id}", $updateData);

        $response->assertStatus(200);
        // Verificamos que el nombre haya sido actualizado en la base de datos
        $this->assertEquals($newName, $user->fresh()->name); 
        
        
        /*// Preparamos otra petición, generamos un nombre muy largo para que no sea aceptado
        $longName = str_repeat($newName, 26); //Lo multiplicamos por 25 (25 es el numero max de caracteres permitidos)
        
        $updateData = ['name' => $longName,];

        $response = $this->actingAs($user, 'api')
                        ->json('PUT', "/api/players/{$user->id}", $updateData);

        $response->assertStatus(422); // Verificamos que la solicitud sea rechazada
        //Verificamos que el nombre no haya sido actualizado en la base de datos
        $this->assertNotEquals($longName, $user->fresh()->name); */
    }
}
