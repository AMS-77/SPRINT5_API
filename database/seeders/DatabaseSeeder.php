<?php

namespace Database\Seeders;
use App\Models\Game;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        User::factory(10)->create();
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(GamesTableSeeder::class);
        Game::factory(10)->create();
    }
}
