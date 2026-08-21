@php($tarjeta = $tarjeta ?? $usuario->tarjetaActual)

<div class="card border-success mb-4">
    <div class="card-header bg-umss-navy text-white font-weight-bold d-flex justify-content-between align-items-center">
        <span>
            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-light mr-2 text-dark"><i class="fas fa-arrow-left"></i> Regresar atrás</a>
            <i class="fas fa-id-badge mr-2"></i>Perfil de Usuario - Tarjeta RFID Activa
        </span>
        <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle"></i> Operativo</span>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Info Usuario y RFID -->
            <div class="col-md-4 text-center border-right">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-5x text-secondary"></i>
                </div>
                <h4>{{ $usuario->nombre }} {{ $usuario->apellido }}</h4>
                <p class="text-muted mb-2">C.I.: {{ $usuario->ci }}</p>
                <div class="p-2 bg-light rounded border mb-2">
                    <small class="text-muted d-block">Código RFID Asignado</small>
                    <strong class="h5 text-primary mb-0"><i class="fas fa-microchip"></i> {{ $tarjeta->codigo_rfid }}</strong>
                </div>
            </div>

            <!-- Saldo y Opciones -->
            <div class="col-md-8">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <span class="text-muted font-weight-bold d-block mb-1">Saldo Actual</span>
                                <h2 class="text-success font-weight-bold mb-0">Bs. {{ number_format($tarjeta->saldo, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center">
                        <button type="button" class="btn btn-umss btn-block py-2 font-weight-bold shadow-sm mb-2" data-toggle="modal" data-target="#modalEditarSaldo">
                            <i class="fas fa-edit mr-2"></i> Editar Saldo Directamente
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-block py-2 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalEditarUsuario">
                            <i class="fas fa-user-edit mr-2"></i> Editar Datos
                        </button>
                    </div>
                </div>

                <!-- Lista Vehículos -->
                <h6 class="font-weight-bold border-bottom pb-1">Vehículos Autorizados</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Placa</th>
                                <th>Modelo</th>
                                <th>Color</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuario->vehiculos as $vehiculo)
                            <tr>
                                <td><strong>{{ $vehiculo->placa }}</strong></td>
                                <td>{{ $vehiculo->marca }}</td>
                                <td>{{ $vehiculo->color }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
