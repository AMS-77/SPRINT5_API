<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        $user = User::factory()->create();  //Creamos un usuario de prueba

        // lo siguiente son datos inválidos
        $userData = [
            'name' => $user->name,  //metemos el mismo nombre que el usuario de prueba
            'email' => 'email_failed',  //formato email incorrecto
            'date' => '2024',  //Fecha inválida.
        ];

        $response = $this->json('POST', '/api/players', $userData);

        //Esperaremos recibir un error 422 que indica que los 3 datos son inválidos
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'date']);
        
    }

}