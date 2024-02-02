<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creamos los roles
        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $rolePlayer = Role::create(['name' => 'player', 'guard_name' => 'api']);

        // Creamos el único usuario administrador de la aplicación
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin')
        ])->assignRole('admin'); 

        // Creamos permisos y se asignan sus roles
        Permission::create(['name' => 'players.playersExitPercentage', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::create(['name' => 'players.playersRanking', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::create(['name' => 'players.lastPlayer', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::create(['name' => 'players.firstPlayer', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);

        Permission::create(['name' => 'players.update', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::create(['name' => 'games.EliminatePlayerRolls', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::create(['name' => 'games.playerRollsDice', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::create(['name' => 'games.listPlaysPlayer', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
    }
}
