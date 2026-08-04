<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;      // ← esta le dice dónde está DB
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // El director llama a los seeders del proyecto, en orden.
        $this->call([
            DatosInicialesSeeder::class,
        ]);
    }
}
