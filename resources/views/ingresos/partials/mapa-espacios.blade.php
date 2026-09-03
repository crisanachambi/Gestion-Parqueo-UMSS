<div class="panel panel-default min-h-full">
   
   <!-- Encabezado del Mapa de Espacios -->
   <div class="panel-heading d-flex justify-content-between align-items-center">
      <div class="panel-title font-weight-bold" style="color: #0b1b3d;">
         SELECCIONAR ESPACIO 
         <small class="text-muted ml-1">— <span id="label-tipo-vehiculo">solo autos</span></small>
      </div>
      <span class="badge badge-success p-2 font-weight-bold" id="badge-disponibles">
         {{ $espaciosLibres->count() ?? 0 }} disponibles
      </span>
   </div>

   <div class="panel-body text-center">

      <!-- Leyenda de Estados y Resumen -->
      <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom text-muted small">
         <div class="font-weight-bold text-dark">
            <span id="texto-resumen-libres">{{ $espaciosLibres->count() ?? 0 }}</span> libres de {{ $totalEspacios ?? 0 }}
         </div>
         <div class="d-flex gap-3 align-items-center">
            <span class="d-inline-flex align-items-center"><em class="fa fa-circle mr-1 text-success" style="font-size: 8px;"></em> Libre</span>
            <span class="d-inline-flex align-items-center"><em class="fa fa-circle mr-1 text-danger" style="font-size: 8px;"></em> Ocupado</span>
            <span class="d-inline-flex align-items-center"><em class="fa fa-circle mr-1 text-muted" style="font-size: 8px;"></em> Mantenim.</span>
         </div>
      </div>

      <!-- Estado Inicial (Vacío antes de escanear RFID) -->
      <div id="empty-space-state" class="pv-xl my-5">
         <em class="fa fa-credit-card fa-3x text-info mb-3"></em>
         <p class="text-muted font-weight-bold">Escanee una tarjeta para ver los espacios disponibles</p>
      </div>

      <!-- Grilla Dinámica de Espacios -->
      <div id="grid-spaces-container" class="hidden">
         <div class="row row-flush" id="contenedor-espacios">
            @forelse($espacios as $espacio)
               @php
                  $esLibre = $espacio->estado === 'libre';
                  $esOcupado = $espacio->estado === 'ocupado';
                  $esMantenimiento = $espacio->estado === 'mantenimiento';

                  // Asignación de clase dinámica
                  $claseEstado = $esLibre ? 'libre' : ($esOcupado ? 'ocupado' : 'mantenimiento');
                  $colorIcono = $esLibre ? 'text-success' : ($esOcupado ? 'text-danger' : 'text-muted');
               @endphp

               <div class="col-xs-6 col-sm-4 col-md-3 p-1 espacio-item" 
                    data-tipo="{{ $espacio->tipo_vehiculo }}"
                    data-estado="{{ $espacio->estado }}">
                  
                  <div class="space-card p-2 text-center {{ $claseEstado }}"
                       @if($esLibre) onclick="seleccionarEspacio({{ $espacio->id }}, '{{ $espacio->numero }}', this)" @endif
                       data-id="{{ $espacio->id }}"
                       data-numero="{{ $espacio->numero }}">

                     <!-- Ícono según vehículo -->
                     @if($espacio->tipo_vehiculo === 'moto')
                        <em class="fa fa-motorcycle fa-lg mb-1 block {{ $colorIcono }}"></em>
                     @else
                        <em class="fa fa-car fa-lg mb-1 block {{ $colorIcono }}"></em>
                     @endif

                     <!-- Código de Espacio -->
                     <strong class="block {{ $esMantenimiento ? 'text-muted' : 'text-dark' }} font-weight-bold" style="font-size: 1.1rem;">
                        {{ $espacio->numero }}
                     </strong>

                     <!-- Etiqueta de Estado -->
                     @if($esLibre)
                        <small class="text-success font-weight-bold block">LIBRE</small>
                     @elseif($esOcupado)
                        <small class="text-danger font-weight-bold block font-monospace">
                           {{ $espacio->ultimoIngreso->placa ?? 'OCUPADO' }}
                        </small>
                     @else
                        <small class="text-muted font-weight-bold block">MANTENIM.</small>
                     @endif

                  </div>
               </div>
            @empty
               <div class="col-xs-12 pv-lg text-muted">
                  <em class="fa fa-exclamation-circle fa-2x mb-2 block"></em>
                  No hay espacios registrados en el sistema.
               </div>
            @endforelse
         </div>
      </div>

   </div>
</div>