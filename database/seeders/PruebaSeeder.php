<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PruebaSeeder extends Seeder
{
    /**
    * DATOS DE PRUEBA.
    * Encargados y clientes inventados para poder trabajar y
    * demostrar el sistema. NO deben cargarse en produccion:
    * basta con comentar la llamada en DatabaseSeeder.
    * Cada encargado se vincula a su parqueo BUSCANDOLO POR NOMBRE,
    * no por posicion en un arreglo. Asi el vinculo no depende del
    * orden en que se hayan creado los parqueos.
    */
    private string $clave = 'parqueo123';
 
    private array $encargados = [
        [
            'nombre'   => 'Ana',
            'apellido' => 'Quispe',
            'email'    => 'economia@umss.bo',
            'telefono' => '70011111',
            'parqueo'  => 'Parqueo Facultad de Economia',
        ],
        [
            'nombre'   => 'Luis',
            'apellido' => 'Mamani',
            'email'    => 'arquitectura@umss.bo',
            'telefono' => '70022222',
            'parqueo'  => 'Parqueo Facultad de Arquitectura',
        ],
        [
            'nombre'   => 'Rocio',
            'apellido' => 'Vargas',
            'email'    => 'canchas@umss.bo',
            'telefono' => '70033333',
            'parqueo'  => 'Parqueo Canchas Deportivas',
        ],
    ];

    public function run(): void
    {
        this->crearEncargados();
        $this->crearClienteCompartido();
        $this->crearClienteSinTarjeta();
 
        $this->command->info("Datos de prueba listos. Clave de encargados: {$this->clave}");
    }

    // ----------------------------------------------------
    // Un encargado por parqueo
    // ----------------------------------------------------
    private function crearEncargados(): void
    {
        foreach ($this->encargados as $e) {
            if (DB::table('usuarios')->where('email', $e['email'])->exists()) {
                continue;
            }
 
            $parqueoId = $this->idDelParqueo($e['parqueo']);
 
            if (! $parqueoId) {
                $this->command->warn("No se encontro el parqueo: {$e['parqueo']}");
                continue;
            }
 
            DB::table('usuarios')->insert([
                'nombre'              => $e['nombre'],
                'apellido'            => $e['apellido'],
                'email'               => $e['email'],
                'telefono'            => $e['telefono'],
                'password'            => Hash::make($this->clave),
                'rol'                 => 'encargado',
                'parqueo_asignado_id' => $parqueoId,
                'activo'              => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }

    // ----------------------------------------------------
    // Cliente con tarjeta en DOS parqueos.
    // Demuestra el concepto central: la persona es unica
    // pero su saldo es independiente por parqueo.
    // ----------------------------------------------------
    private function crearClienteCompartido(): void
    {
        if (DB::table('usuarios')->where('ci', '8452136')->exists()) {
            return;
        }
 
        $economia     = $this->idDelParqueo('Parqueo Facultad de Economia');
        $arquitectura = $this->idDelParqueo('Parqueo Facultad de Arquitectura');
 
        $clienteId = DB::table('usuarios')->insertGetId([
            'nombre'            => 'Carlos',
            'apellido'          => 'Rojas',
            'ci'                => '8452136',
            'telefono'          => '70012345',
            'categoria'         => 'estudiante',
            'rol'               => 'usuario',
            'parqueo_origen_id' => $economia,   // Economia lo registro
            'activo'            => 1,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
 
        DB::table('vehiculos')->insert([
            'usuario_id' => $clienteId,
            'placa'      => '3421ABC',
            'tipo'       => 'auto',
            'marca'      => 'Toyota',
            'color'      => 'Blanco',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
 
        // Dos plasticos distintos, dos saldos que no se mezclan.
        DB::table('tarjetas')->insert([
            [
                'usuario_id'  => $clienteId,
                'parqueo_id'  => $economia,
                'codigo_rfid' => 'RFID-ECO-0001',
                'saldo'       => 50.00,
                'estado'      => 'activa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'usuario_id'  => $clienteId,
                'parqueo_id'  => $arquitectura,
                'codigo_rfid' => 'RFID-ARQ-0001',
                'saldo'       => 20.00,
                'estado'      => 'activa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    // ----------------------------------------------------
    // Usuario que solo se bajo la app: sin tarjeta.
    // Sirve para probar el estado vacio de la app movil.
    // ----------------------------------------------------
    private function crearClienteSinTarjeta(): void
    {
        if (DB::table('usuarios')->where('ci', '9871234')->exists()) {
            return;
        }
 
        $usuarioId = DB::table('usuarios')->insertGetId([
            'nombre'            => 'Maria',
            'apellido'          => 'Flores',
            'ci'                => '9871234',
            'email'             => 'maria@estudiante.umss.edu.bo',
            'password'          => Hash::make($this->clave),
            'telefono'          => '70098765',
            'categoria'         => 'estudiante',
            'rol'               => 'usuario',
            'parqueo_origen_id' => null,   // se registro sola desde la app
            'activo'            => 1,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
 
        DB::table('vehiculos')->insert([
            'usuario_id' => $usuarioId,
            'placa'      => '7788XYZ',
            'tipo'       => 'moto',
            'marca'      => 'Honda',
            'color'      => 'Rojo',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
 
        // A proposito NO se le crea tarjeta.
    }


    private function idDelParqueo(string $nombre): ?int
    {
        return DB::table('parqueos')->where('nombre', $nombre)->value('id');
    }
}
