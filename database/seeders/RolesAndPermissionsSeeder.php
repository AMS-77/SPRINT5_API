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
        $roleAdmin = Role::firstOrcreate(['name' => 'admin', 'guard_name' => 'api']);
        $rolePlayer = Role::firstOrcreate(['name' => 'player', 'guard_name' => 'api']);

        // Creamos el único usuario administrador de la aplicación si no existe
        $admin = User::where('name', 'admin')->first();

        if (!$admin) {
            $admin = User::firstOrCreate([
                'email' => 'admin@admin.com',
            ], [
                'name' => 'admin',
                'password' => bcrypt('admin'),
                'date' => now()
            ]);
        }

        $admin->assignRole($roleAdmin);

        // Creamos permisos y se asignan sus roles
        Permission::firstOrCreate(['name' => 'players.playersExitPercentage', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::firstOrCreate(['name' => 'players.playersRanking', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::firstOrCreate(['name' => 'players.lastPlayer', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);
        Permission::firstOrCreate(['name' => 'players.firstPlayer', 'guard_name' => 'api'])->syncRoles([$roleAdmin]);

        Permission::firstOrCreate(['name' => 'players.update', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::firstOrCreate(['name' => 'games.EliminatePlayerRolls', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::firstOrCreate(['name' => 'games.playerRollsDice', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
        Permission::firstOrCreate(['name' => 'games.listPlaysPlayer', 'guard_name' => 'api'])->syncRoles([$rolePlayer]);
    }
}
