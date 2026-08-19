<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
    <h5 class="m-0 font-weight-bold text-dark" style="font-size: 14px; letter-spacing: 0.5px;">VEHÍCULOS DENTRO</h5>
    <span class="badge badge-pill badge-primary font-weight-bold px-2 py-1">{{ count($vehiculosDentro) }}</span>
  </div>

  <div class="card-body p-0">
    <div class="list-group list-group-flush">
      @forelse($vehiculosDentro as $vehiculo)
        <div class="list-group-item border-0 p-3 d-flex align-items-center justify-content-between vehicle-item">

          <div class="d-flex align-items-center">
            {{-- Icono segun el tipo real del vehiculo --}}
            <div class="vehicle-icon-box mr-3">
              <i class="fa {{ $vehiculo->tipo === 'moto' ? 'fa-motorcycle' : 'fa-car' }} text-danger"></i>
            </div>

            <div>
              <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $vehiculo->placa }}</div>
              <div class="text-muted" style="font-size: 12px;">{{ $vehiculo->usuario_nombre }}</div>
              <small class="text-muted" style="font-size: 11px;">
                Esp. {{ $vehiculo->espacio_codigo }} · desde {{ $vehiculo->hora_ingreso }}
              </small>
            </div>
          </div>

          {{-- Tiempo dentro, cobro estimado y metodo de pago --}}
          <div class="text-right">
            <div class="font-weight-bold text-warning" style="font-size: 12px;">
              {{ $vehiculo->tiempo_transcurrido }}
            </div>
            <div class="font-weight-bold text-dark" style="font-size: 12px;">
              {{ number_format($vehiculo->cobro_estimado['monto'], 2) }} Bs
            </div>
            <span class="badge {{ $vehiculo->metodo_pago === 'RFID' ? 'badge-light-primary' : 'badge-light-success' }}" style="font-size: 10px;">
              {{ $vehiculo->metodo_pago }}
            </span>
          </div>

        </div>
      @empty
        <div class="p-4 text-center text-muted">
          <i class="fa fa-car fa-2x mb-2 opacity-50 d-block"></i>
          <p class="m-0 small">No hay vehículos dentro en este momento.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>