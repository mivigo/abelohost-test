<?php

namespace Database\Seeders;

use App\Core\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        echo "Running CategorySeeder...\n";
        (new CategorySeeder())->run();

        echo "Running PostSeeder...\n";
        (new PostSeeder())->run();
    }
}
