{{-- PARTIAL: solo el mapa. Sin @extends ni @section, porque
     esta vista se incluye dentro de dashboard/index.blade.php --}}
<div class="card card-default">
   <div class="card-body">

      <!-- ENCABEZADO INTERNO Y LEYENDA -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
         <div>
            <h4 class="font-weight-bold m-0" style="color: #1c0b3d;">MAPA DE ESPACIOS</h4>
            <small class="text-muted">
               <strong class="text-success">{{ $disponiblesCount }} libres</strong> de {{ $capacidadTotal }} espacios
            </small>
         </div>

         <div class="d-flex flex-column align-items-md-end mt-2 mt-md-0">
            <div class="mb-2">
               <span class="badge-status-pill badge-status-libre mr-1">{{ $disponiblesCount }} libres</span>
               <span class="badge-status-pill badge-status-ocupado">{{ $ocupadosCount }} ocupados</span>
            </div>
            <div class="d-flex align-items-center small text-muted">
               <span class="mr-3"><span class="dot-indicator dot-libre mr-1"></span> Libre</span>
               <span><span class="dot-indicator dot-ocupado mr-1"></span> Ocupado</span>
            </div>
         </div>
      </div>

      <!-- SECCIÓN 1: ESPACIOS PARA AUTOS -->
      <div class="mb-4">
         <h6 class="text-uppercase font-weight-bold text-muted mb-3">
            <i class="fas fa-car mr-2"></i>AUTOS
         </h6>
         <div class="espacios-grid-container">
            @forelse($espaciosAutos as $espacio)
               <div class="espacio-item-box estado-{{ $espacio->estado }}">
                  <i class="fas fa-car icono-vehiculo"></i>
                  <span class="codigo-espacio">{{ $espacio->codigo }}</span>
                  @if($espacio->estado === 'ocupado' && $espacio->placa)
                     <span class="placa-vehiculo">{{ $espacio->placa }}</span>
                  @endif
               </div>
            @empty
               <p class="text-muted small">No hay espacios de autos registrados.</p>
            @endforelse
         </div>
      </div>

      <!-- SECCIÓN 2: ESPACIOS PARA MOTOS -->
      <div>
         <h6 class="text-uppercase font-weight-bold text-muted mb-3">
            <i class="fas fa-motorcycle mr-2"></i>MOTOS
         </h6>
         <div class="espacios-grid-container">
            @forelse($espaciosMotos as $espacio)
               <div class="espacio-item-box estado-{{ $espacio->estado }}">
                  <i class="fas fa-motorcycle icono-vehiculo"></i>
                  <span class="codigo-espacio">{{ $espacio->codigo }}</span>
                  @if($espacio->estado === 'ocupado' && $espacio->placa)
                     <span class="placa-vehiculo">{{ $espacio->placa }}</span>
                  @endif
               </div>
            @empty
               <p class="text-muted small">No hay espacios de motos registrados.</p>
            @endforelse
         </div>
      </div>

   </div>
</div>