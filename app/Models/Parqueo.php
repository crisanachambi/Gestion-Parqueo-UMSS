<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Auditable;
use Carbon\Carbon;

class Parqueo extends Model
{
    use SoftDeletes, Auditable; 

    protected $table = 'parqueos';
    protected $guarded = ['id'];

    protected $casts = [
        'tarifa_auto'        => 'decimal:2',
        'tarifa_moto'        => 'decimal:2',
        'multa_nocturna'     => 'decimal:2',
        'tolerancia_minutos' => 'integer',
        'activo'             => 'boolean',
    ];

    // ---------------- Relaciones ----------------
 
    public function periodos()
    {
        return $this->hasMany(Periodo::class)->orderBy('orden');
    }
 
    public function espacios()
    {
        return $this->hasMany(Espacio::class);
    }
 
    public function tarjetas()
    {
        return $this->hasMany(Tarjeta::class);
    }
 
    public function encargado()
    {
        return $this->hasOne(Usuario::class, 'parqueo_asignado_id')
                    ->where('rol', 'encargado');
    }
 
    // ---------------- Logica de cobro ----------------
 
    /**
     * Cuantos periodos del dia ocupa la estadia.
     * Un periodo cuenta si se solapa con el rango de la
     * estadia. Los bordes no cuentan: entrar a las 11:30
     * exactas no incluye el periodo que termina a esa hora.
     */
    public function periodosOcupados(Carbon $ingreso, Carbon $salida): int
    {
        return $this->periodos()
            ->where('hora_inicio', '<', $salida->format('H:i:s'))
            ->where('hora_fin', '>', $ingreso->format('H:i:s'))
            ->count();
    }
 
    /**
     * Tarifa por periodo segun el tipo de vehiculo.
     */
    public function tarifaPara(string $tipo): float
    {
        return (float) ($tipo === 'auto' ? $this->tarifa_auto : $this->tarifa_moto);
    }
    
    /**
     * Devuelve el detalle del cobro de una estadia.
     * No guarda nada: solo calcula. El controlador decide
     * que hacer con el resultado.
     *
     * @return array{periodos:int, monto:float, pernocto:bool, minutos:int}
     */

    public function calcularCobro(Carbon $ingreso, Carbon $salida, string $tipo): array
    {
        $minutos = $ingreso->diffInMinutes($salida);

        // Estadia muy corta: no se cobra. Con tolerancia en 0
        // (valor por defecto) esta excepcion nunca aplica.
        if ($this->tolerancia_minutos > 0 && $minutos <= $this->tolerancia_minutos) {
            return [
                'periodos' => 0,
                'monto'    => 0.0,
                'pernocto' => false,
                'minutos'  => $minutos,
            ];
        }

        // COBRO MINIMO: siempre se paga al menos un periodo.
        // Sin esto, una estadia fuera de los horarios definidos
        // devolveria 0 periodos y el encargado no cobraria nada.
        $periodos = max(1, $this->periodosOcupados($ingreso, $salida));
        $monto    = $periodos * $this->tarifaPara($tipo);

        // Pernocte: salio despues del cierre o en otro dia.
        $pernocto = ! $ingreso->isSameDay($salida)
                 || $salida->format('H:i:s') > $this->horario_cierre;

        if ($pernocto) {
            $monto += (float) $this->multa_nocturna;
        }

        return [
            'periodos' => $periodos,
            'monto'    => round($monto, 2),
            'pernocto' => $pernocto,
            'minutos'  => $minutos,
        ];
    }

    // ---------------- Disponibilidad ----------------
 
    /**
     * Espacios libres por tipo. Lo consume la app movil.
     */
    public function disponibilidad(): array
    {
        $conteo = $this->espacios()
            ->selectRaw("tipo, SUM(estado = 'libre') AS libres, COUNT(*) AS total")
            ->groupBy('tipo')
            ->get()
            ->keyBy('tipo');
 
        return [
            'auto' => [
                'libres' => (int) ($conteo['auto']->libres ?? 0),
                'total'  => (int) ($conteo['auto']->total ?? 0),
            ],
            'moto' => [
                'libres' => (int) ($conteo['moto']->libres ?? 0),
                'total'  => (int) ($conteo['moto']->total ?? 0),
            ],
        ];
    }

}
