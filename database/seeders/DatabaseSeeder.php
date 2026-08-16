<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
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
    }
}
