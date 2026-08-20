<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-id-card mr-1"></i> Control de Acceso RFID (ESP32)
        </h6>
    </div>
    <div class="card-body">
        
        @if($ultimoMovimiento)
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold border-bottom pb-2">Último Movimiento RFID</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>UID:</strong> <span>{{ $ultimoMovimiento->tarjeta->codigo_rfid }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Usuario:</strong> <span>{{ $ultimoMovimiento->tarjeta->usuario->nombre_completo }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Vehículo:</strong> <span>{{ $ultimoMovimiento->vehiculo->marca }} ({{ $ultimoMovimiento->vehiculo->placa }})</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Acción:</strong> 
                            @if($ultimoMovimiento->estado === 'activo')
                                <span class="badge badge-success text-uppercase">Entrada</span>
                            @else
                                <span class="badge badge-secondary text-uppercase">Salida</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Espacio:</strong> <span class="font-weight-bold">{{ $ultimoMovimiento->espacio->numero }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Hora:</strong> <span>{{ $ultimoMovimiento->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Estado:</strong> 
                            <span class="text-uppercase">{{ $ultimoMovimiento->estado }}</span>
                        </li>
                        @if($ultimoMovimiento->estado === 'finalizado' && $ultimoMovimiento->pago)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Monto:</strong> <span class="text-danger font-weight-bold">Bs {{ number_format($ultimoMovimiento->pago->monto, 2) }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif

        <h5 class="font-weight-bold border-bottom pb-2">Últimos Movimientos RFID</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>UID</th>
                        <th>Usuario</th>
                        <th>Vehículo</th>
                        <th>Espacio</th>
                        <th>Acción</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientosRfid as $movimiento)
                        <tr>
                            <td>{{ $movimiento->updated_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge badge-info">{{ $movimiento->tarjeta->codigo_rfid }}</span></td>
                            <td>{{ $movimiento->tarjeta->usuario->nombre_completo }}</td>
                            <td>{{ $movimiento->vehiculo->placa }}</td>
                            <td><strong>{{ $movimiento->espacio->numero }}</strong></td>
                            <td>
                                @if($movimiento->estado === 'activo')
                                    <span class="text-success font-weight-bold">ENTRADA</span>
                                @else
                                    <span class="text-secondary font-weight-bold">SALIDA</span>
                                @endif
                            </td>
                            <td>{{ strtoupper($movimiento->estado) }}</td>
                            <td>
                                @if($movimiento->estado === 'finalizado' && $movimiento->pago)
                                    Bs {{ number_format($movimiento->pago->monto, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay movimientos RFID registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
