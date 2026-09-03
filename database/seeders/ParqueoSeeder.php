<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParqueoSeeder extends Seeder
{
   /**
   * DATOS REALES DEL SISTEMA.
   * Los 3 parqueos del campus, sus horarios de cobro y sus
   * espacios fisicos. Esto NO son datos de prueba: existe en la
   * realidad y seguira existiendo cuando el sistema entre en uso.
   * Es IDEMPOTENTE: se puede correr las veces que haga falta sin
   * duplicar nada, porque busca antes de insertar.
   */
    private array $parqueos = [
        [
            'nombre'    => 'Parqueo Facultad de Economia',
            'ubicacion' => 'Campus Central UMSS',
            'espacios'  => ['auto' => 20, 'moto' => 10],
        ],
        [
            'nombre'    => 'Parqueo Facultad de Arquitectura',
            'ubicacion' => 'Campus Central UMSS',
            'espacios'  => ['auto' => 25, 'moto' => 15],
        ],
        [
            'nombre'    => 'Parqueo Canchas Deportivas',
            'ubicacion' => 'Campus Central UMSS',
            'espacios'  => ['auto' => 15, 'moto' => 10],
        ],
    ];
 
    /**
     * Jornada 06:30 - 21:30 (15 h) en 3 bloques de 5 h.
     * Igual para los 3 parqueos, pero cada uno guarda los
     * suyos para poder cambiarlos por separado despues.
     */
    private array $periodos = [
        ['nombre' => 'Manana', 'hora_inicio' => '06:30:00', 'hora_fin' => '11:30:00', 'orden' => 1],
        ['nombre' => 'Tarde',  'hora_inicio' => '11:30:00', 'hora_fin' => '16:30:00', 'orden' => 2],
        ['nombre' => 'Noche',  'hora_inicio' => '16:30:00', 'hora_fin' => '21:30:00', 'orden' => 3],
    ];

    public function run(): void
    {
       foreach ($this->parqueos as $datos) {
            $parqueoId = $this->crearParqueo($datos);
            $this->crearPeriodos($parqueoId);
            $this->crearEspacios($parqueoId, $datos['espacios']);
        }
 
        $this->command->info('Parqueos, periodos y espacios listos.'); 
    }
    
    /**
     * Busca el parqueo por nombre. Si existe lo devuelve, si no
     * lo crea. Asi correr el seeder dos veces no duplica nada.
     */
    private function crearParqueo(array $datos): int
    {
        $existente = DB::table('parqueos')
                       ->where('nombre', $datos['nombre'])
                       ->first();
 
        if ($existente) {
            return $existente->id;
        }
 
        return DB::table('parqueos')->insertGetId([
            'nombre'             => $datos['nombre'],
            'ubicacion'          => $datos['ubicacion'],
            'tarifa_auto'        => 4.00,   // Bs por periodo
            'tarifa_moto'        => 1.00,   // Bs por periodo
            'horario_apertura'   => '06:30:00',
            'horario_cierre'     => '21:30:00',
            'tolerancia_minutos' => 0,      // se cobra siempre
            'multa_nocturna'     => 0.00,
            'activo'             => 1,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    private function crearPeriodos(int $parqueoId): void
    {
        foreach ($this->periodos as $p) {
            $existe = DB::table('periodos')
                        ->where('parqueo_id', $parqueoId)
                        ->where('orden', $p['orden'])
                        ->exists();
 
            if ($existe) {
                continue;
            }
 
            DB::table('periodos')->insert($p + [
                'parqueo_id' => $parqueoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function crearEspacios(int $parqueoId, array $cantidades): void
    {
        $prefijos = ['auto' => 'A', 'moto' => 'M'];
        $filas    = [];
 
        foreach ($cantidades as $tipo => $cantidad) {
            for ($n = 1; $n <= $cantidad; $n++) {
                $numero = $prefijos[$tipo] . '-' . str_pad($n, 2, '0', STR_PAD_LEFT);
 
                $existe = DB::table('espacios')
                            ->where('parqueo_id', $parqueoId)
                            ->where('numero', $numero)
                            ->exists();
 
                if ($existe) {
                    continue;
                }
 
                $filas[] = [
                    'parqueo_id' => $parqueoId,
                    'numero'     => $numero,
                    'tipo'       => $tipo,
                    'estado'     => 'libre',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
 
        if ($filas) {
            DB::table('espacios')->insert($filas);
        }
    }
}
