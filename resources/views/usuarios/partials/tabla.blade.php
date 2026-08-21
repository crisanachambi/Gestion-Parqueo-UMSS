<div class="card card-default">
    <div class="card-header bg-umss-navy text-white d-flex justify-content-between align-items-center">
        <span class="font-weight-bold"><i class="fas fa-users mr-2"></i>Catálogo General de Usuarios</span>
        <span class="badge badge-light text-dark">{{ $usuarios->count() ?? 0 }} Registrados</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>C.I.</th>
                        <th>Nombre Completo</th>
                        <th>Tipo Usuario</th>
                        <th>Tarjeta RFID</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                    <tr>
                        <td><strong>{{ $user->ci }}</strong></td>
                        <td>{{ $user->nombre }} {{ $user->apellido }}</td>
                        <td><span class="badge badge-info">{{ $user->categoria }}</span></td>
                        <td>
                            @if($user->rfid_code)
                                <span class="badge badge-success"><i class="fas fa-microchip"></i> {{ $user->tarjetaActual?->codigo_rfid }}</span>
                            @else
                                <span class="badge badge-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Sin Tarjeta</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->activo ? 'success' : 'danger' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('usuarios.buscar', ['ci' => $user->ci]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-2x d-block mb-2"></i> No se encontraron usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>