<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 

class DatosInicialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Usuario que será encargado. Guardamos su ID para reutilizarlo.
        $usuarioId = DB::table('usuarios')->insertGetId([
            'nombre'     => 'Encargado Economía',
            'email'      => 'economia@parkumss.bo',
            'password'   => Hash::make('12345678'),   // la contraseña se guarda cifrada, nunca en texto plano
            'rol'        => 'encargado',
            'telefono'   => '77908014',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2) Parqueo que administrará ese encargado.
        $parqueoId = DB::table('parqueos')->insertGetId([
            'nombre'              => 'Parqueo Facultad de Economía',
            'ubicacion'           => 'Campus Central UMSS',
            'capacidad_total'     => 50,
            'tarifa_moto_periodo' => 2.00,
            'tarifa_auto_periodo' => 4.00,
            'horario_apertura'    => '06:30:00',
            'horario_cierre'      => '21:30:00',
            'created_by'          => $usuarioId,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // 3) Vínculo usuario <-> parqueo en la tabla bisagra.
        DB::table('encargados')->insert([
            'usuario_id' => $usuarioId,
            'parqueo_id' => $parqueoId,
            'created_by' => $usuarioId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
         // 4) Algunos espacios de prueba para el parqueo de Economía.
        for ($i = 1; $i <= 10; $i++) {
            DB::table('espacios')->insert([
                'parqueo_id' => $parqueoId,
                'numero'     => 'A-' . $i,
                'tipo'       => 'auto',
                // Dejamos algunos ocupados para que el dashboard muestre variedad:
                'estado'     => $i <= 3 ? 'ocupado' : 'libre',
                'created_by' => $usuarioId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
