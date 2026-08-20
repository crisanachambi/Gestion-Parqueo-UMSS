<div class="card border-warning mb-4">
    <div class="card-header bg-warning text-dark font-weight-bold d-flex justify-content-between align-items-center">
        <span><i class="fas fa-exclamation-circle mr-2"></i>Usuario Encontrado - Sin Tarjeta RFID Asignada</span>
        <span class="badge badge-dark">C.I.: {{ $usuario->ci }}</span>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Datos Personales -->
            <div class="col-md-6 border-right">
                <h5 class="text-navy font-weight-bold border-bottom pb-2">Datos Personales</h5>
                <p class="mb-1"><strong>Nombre:</strong> {{ $usuario->nombre }} {{ $usuario->apellido }}</p>
                <p class="mb-1"><strong>Correo:</strong> {{ $usuario->email }}</p>
                <p class="mb-1"><strong>Teléfono:</strong> {{ $usuario->telefono ?? 'No registrado' }}</p>
                <p class="mb-0"><strong>Tipo:</strong> <span class="badge badge-info">{{ $usuario->categoria }}</span></p>
            </div>

            <!-- Vehículos Registrados -->
            <div class="col-md-6">
                <h5 class="text-navy font-weight-bold border-bottom pb-2">Vehículos Asociados</h5>
                <ul class="list-group list-group-flush mb-3">
                    @forelse($usuario->vehiculos as $vehiculo)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                            <span><i class="fas fa-car mr-2"></i><strong>{{ $vehiculo->placa }}</strong> - {{ $vehiculo->marca }}</span>
                            <span class="badge badge-secondary">{{ $vehiculo->tipo }}</span>
                        </li>
                    @empty
                        <li class="list-group-item px-0 text-muted">Sin vehículos registrados.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        
        <hr>

        <!-- Botón de Asignación Directa -->
        <div class="text-right">
            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modalAsignarRfid">
                <i class="fas fa-plus-circle mr-1"></i> Emitir / Asignar Tarjeta RFID
            </button>
        </div>
    </div>
</div>