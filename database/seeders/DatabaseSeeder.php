<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Run a specific seeder...
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            // CommentSeeder::class,
        ]);

    }
}
