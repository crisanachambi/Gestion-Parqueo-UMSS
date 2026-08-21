@extends('layouts.app')

@section('title', 'Registrar Salidas - ParkUMSS')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/salidas.css') }}">
@endpush

@section('content')
<div class="container-fluid py-2">

   <!-- Encabezado Principal -->
   <div class="content-heading mb-4">
      <h3 class="m-0 font-weight-bold text-dark">Registrar salidas</h3>
      <small class="text-muted">Vehículos actualmente dentro de <strong>{{ $parqueo->nombre }}</strong></small>
   </div>

   <!-- Contenedor Principal (Tarjeta Estilizada) -->
   <div class="card border-0 shadow-sm rounded-lg">
      
      <!-- Cabecera de la Tabla -->
      <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
         <span class="font-weight-bold text-uppercase text-dark" style="letter-spacing: 0.5px; font-size: 0.9rem;">
            INGRESOS ACTIVOS
         </span>

         <div class="d-flex align-items-center">
            <span class="badge badge-pill border-0 px-3 py-2 mr-2 font-weight-bold" style="background-color: #e8f1ff; color: #1a73e8; font-size: 0.85rem;">
               {{ $activos->count() }} vehículos
            </span>
            <small class="text-muted font-weight-normal">Cobro estimado actualizado cada 10s</small>
         </div>
      </div>

      <!-- Cuerpo de la Tabla con todas las columnas -->
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
               <thead class="bg-light text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                  <tr>
                     <th class="pl-4">Placa</th>
                     <th>Tipo</th>
                     <th>Espacio</th>
                     <th>Hora Entrada</th>
                     <th>Transcurrido</th>
                     <th class="text-center">Periodos</th>
                     <th>Cobro Estimado</th>
                     <th>Modo</th>
                     <th>Usuario / Titular</th>
                     <th class="text-right pr-4">Acción</th>
                  </tr>
               </thead>
               <tbody>
               @forelse($activos as $registro)
                  <tr>
                     <!-- 1. Placa -->
                     <td class="pl-4 font-weight-bold h6 text-dark mb-0 align-middle">
                        {{ $registro->placa }}
                     </td>

                     <!-- 2. Tipo -->
                     <td class="align-middle">
                        @if(($registro->vehiculo?->tipo ?? '') === 'moto' || \Illuminate\Support\Str::startsWith($registro->espacio?->numero, 'M'))
                           <span class="badge badge-light border text-warning px-2 py-1 font-weight-bold">
                              <i class="fas fa-motorcycle mr-1"></i> Moto
                           </span>
                        @else
                           <span class="badge badge-light border text-primary px-2 py-1 font-weight-bold">
                              <i class="fas fa-car mr-1"></i> Auto
                           </span>
                        @endif
                     </td>

                     <!-- 3. Espacio -->
                     <td class="align-middle">
                        <span class="badge badge-dark px-2 py-1" style="font-size: 0.85rem;">
                           {{ $registro->espacio?->numero ?? 'S/E' }}
                        </span>
                     </td>

                     <!-- 4. Hora Entrada -->
                     <td class="align-middle text-muted">
                        <small><i class="far fa-clock mr-1"></i> {{ $registro->hora_ingreso->format('h:i A') }}</small>
                     </td>

                     <!-- 5. Transcurrido -->
                     <td class="align-middle">
                        <span class="text-dark font-weight-bold">
                           <i class="far fa-hourglass mr-1 text-info"></i> {{ $registro->transcurrido ?? $registro->hora_ingreso->diffForHumans(null, true, true) }}
                        </span>
                     </td>

                     <!-- 6. Periodos -->
                     <td class="align-middle text-center">
                        <span class="badge badge-light border font-weight-bold">
                           {{ $registro->estimado['periodos'] ?? $registro->periodos ?? 1 }}
                        </span>
                     </td>

                     <!-- 7. Cobro Estimado -->
                     <td class="align-middle">
                        <strong class="text-success h6 font-weight-bold mb-0">
                           Bs. {{ number_format($registro->estimado['monto'], 2) }}
                        </strong>
                     </td>

                     <!-- 8. Modo -->
                     <!-- 8. Modo -->
<td class="align-middle">
   @if(($registro->tipo_ingreso ?? $registro->modo) === 'tarjeta' || !$registro->esVisitante())
      <span class="badge badge-info px-2 py-1">
         <i class="fas fa-credit-card mr-1"></i> Tarjeta
      </span>
   @else
      <span class="badge badge-success px-2 py-1">
         <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
      </span>
   @endif
</td>

                     <!-- 9. Usuario / Titular -->
                     <td class="align-middle">
                        @if($registro->esVisitante())
                           <span class="text-muted font-italic">Visitante</span>
                        @else
                           <span class="font-weight-bold text-dark">{{ $registro->tarjeta->usuario->nombre_completo }}</span>
                        @endif
                     </td>

                     <!-- 10. Acción -->
                     <td class="text-right pr-4 align-middle">
                        <button type="button" class="btn btn-success btn-sm px-3 shadow-sm font-weight-bold" onclick="abrirModalSalida({{ $registro->id }})">
                           <i class="fas fa-sign-out-alt mr-1"></i> Registrar salida
                        </button>
                     </td>
                  </tr>
               @empty
                  <tr>
                     <td colspan="10" class="text-center text-muted py-5">
                        <i class="fas fa-car-side fa-3x mb-3 text-muted"></i>
                        <p class="mb-0 font-weight-bold">No hay vehículos registrados dentro del parqueo en este momento.</p>
                     </td>
                  </tr>
               @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div>

</div>

@include('salidas.modal_salida')
@endsection

@push('scripts')
<script src="{{ asset('js/salidas.js') }}"></script>
@endpush