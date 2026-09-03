<?php

namespace Database\Seeders;

<<<<<<< HEAD
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 02f17e8fea3785350b13a04082ae7d35fec22650
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
<<<<<<< HEAD
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
=======
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // Datos REALES: los 3 parqueos, sus horarios de
            // cobro y sus espacios fisicos. Siempre se cargan.
            ParqueoSeeder::class,
 
            // Datos de PRUEBA: encargados y clientes inventados.
            // Comentar esta linea al pasar a produccion.
            PruebaSeeder::class,
        ]);
>>>>>>> 02f17e8fea3785350b13a04082ae7d35fec22650
    }
}
