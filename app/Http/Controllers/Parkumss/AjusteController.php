<?php

namespace App\Http\Controllers\Parkumss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Configuracion del parqueo: tarifas, periodos, espacios y
 * perfil del encargado. Todo lo que se edita aqui son VALORES, no logica: por eso
 * viven en la base y no en el codigo. Cambiar una tarifa no
 * deberia requerir tocar el sistema.
 */

class AjusteController extends Controller
{
    public function index()
    {
        $parqueo = auth()->user()->parqueoAsignado;
 
        return view('ajustes.index', [
            'parqueo'  => $parqueo,
            'periodos' => Periodo::orderBy('orden')->get(),
            'espacios' => Espacio::orderBy('numero')->get(),
            'usuario'  => auth()->user(),
        ]);
    }
 
    /**
     * Tarifas y reglas de cobro del parqueo.
     */
    public function tarifas(Request $request)
    {
        $datos = $request->validate([
            'tarifa_auto'        => ['required', 'numeric', 'min:0', 'max:9999'],
            'tarifa_moto'        => ['required', 'numeric', 'min:0', 'max:9999'],
            'tolerancia_minutos' => ['required', 'integer', 'min:0', 'max:120'],
            'multa_nocturna'     => ['required', 'numeric', 'min:0', 'max:9999'],
        ]);
 
        auth()->user()->parqueoAsignado->update($datos);
 
        return back()->with('exito', 'Tarifas actualizadas.');
    }
 
    /**
     * Horarios de los 3 periodos.
     *
     * La base valida que hora_fin > hora_inicio, pero el
     * solapamiento entre periodos solo se puede comprobar aqui:
     * un CHECK solo ve su propia fila, no las demas.
     */
    public function periodos(Request $request)
    {
        $datos = $request->validate([
            'periodos'               => ['required', 'array', 'size:3'],
            'periodos.*.id'          => ['required', 'exists:periodos,id'],
            'periodos.*.nombre'      => ['required', 'string', 'max:40'],
            'periodos.*.hora_inicio' => ['required', 'date_format:H:i'],
            'periodos.*.hora_fin'    => ['required', 'date_format:H:i', 'after:periodos.*.hora_inicio'],
        ]);
 
        if ($error = $this->haySolapamiento($datos['periodos'])) {
            return back()->withErrors($error);
        }
 
        DB::transaction(function () use ($datos) {
            foreach ($datos['periodos'] as $p) {
                Periodo::where('id', $p['id'])->update([
                    'nombre'      => $p['nombre'],
                    'hora_inicio' => $p['hora_inicio'] . ':00',
                    'hora_fin'    => $p['hora_fin'] . ':00',
                ]);
            }
        });
 
        return back()->with('exito', 'Horarios actualizados.');
    }
 
    /**
     * Alta de un espacio fisico.
     */
    public function crearEspacio(Request $request)
    {
        $parqueoId = auth()->user()->parqueo_asignado_id;
 
        $datos = $request->validate([
            'numero' => [
                'required', 'string', 'max:10',
                // Unico dentro del parqueo, solo entre vigentes.
                Rule::unique('espacios')
                    ->where(fn ($q) => $q->where('parqueo_id', $parqueoId)
                                         ->whereNull('deleted_at')),
            ],
            'tipo'   => ['required', 'in:moto,auto'],
        ], [
            'numero.unique' => 'Ya existe un espacio con ese numero en este parqueo.',
        ]);
 
        Espacio::create($datos + ['estado' => 'libre']);
 
        return back()->with('exito', "Espacio {$datos['numero']} creado.");
    }
 
    /**
     * Cambia el estado de un espacio: libre, ocupado o
     * mantenimiento. No permite tocar uno que tenga un
     * vehiculo dentro, porque dejaria el registro huerfano.
     */
    public function estadoEspacio(Request $request, Espacio $espacio)
    {
        $request->validate([
            'estado' => ['required', 'in:libre,ocupado,mantenimiento'],
        ]);
 
        if ($espacio->registroActivo) {
            return back()->withErrors(
                "El espacio {$espacio->numero} tiene un vehiculo dentro. " .
                'Registre su salida antes de cambiar el estado.'
            );
        }
 
        $espacio->update(['estado' => $request->estado]);
 
        return back()->with('exito', "Espacio {$espacio->numero}: {$request->estado}.");
    }
 
    /**
     * Baja logica de un espacio. El soft delete libera su
     * numero gracias a la columna `vigente`, asi que se puede
     * volver a crear uno con el mismo codigo.
     */
    public function eliminarEspacio(Espacio $espacio)
    {
        if ($espacio->registroActivo) {
            return back()->withErrors('No se puede eliminar un espacio ocupado.');
        }
 
        $espacio->delete();
 
        return back()->with('exito', "Espacio {$espacio->numero} dado de baja.");
    }
 
    /**
     * Datos del encargado. No incluye rol ni parqueo asignado:
     * si pudiera cambiarse el parqueo, se saltaria todo el
     * aislamiento del Global Scope.
     */
    public function perfil(Request $request)
    {
        $usuario = auth()->user();
 
        $datos = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email'    => [
                'required', 'email', 'max:150',
                Rule::unique('usuarios')->ignore($usuario->id)->whereNull('deleted_at'),
            ],
        ]);
 
        $usuario->update($datos);
 
        return back()->with('exito', 'Perfil actualizado.');
    }
 
    public function password(Request $request)
    {
        $datos = $request->validate([
            'actual' => ['required', 'current_password'],
            'nueva'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'actual.current_password' => 'La contrasena actual no es correcta.',
            'nueva.confirmed'         => 'La confirmacion no coincide.',
        ]);
 
        auth()->user()->update(['password' => Hash::make($datos['nueva'])]);
 
        return back()->with('exito', 'Contrasena actualizada.');
    }
 
    /**
     * Dos periodos no pueden pisarse: si lo hicieran, una
     * estadia contaria periodos de mas y se cobraria doble.
     */
    private function haySolapamiento(array $periodos): ?string
    {
        foreach ($periodos as $i => $a) {
            foreach (array_slice($periodos, $i + 1) as $b) {
                if ($a['hora_inicio'] < $b['hora_fin'] && $a['hora_fin'] > $b['hora_inicio']) {
                    return "Los periodos {$a['nombre']} y {$b['nombre']} se solapan.";
                }
            }
        }
 
        return null;
    }
}
