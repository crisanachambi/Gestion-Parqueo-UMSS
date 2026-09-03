<div class="modal fade" id="modalEditarSaldo" tabindex="-1" role="dialog" aria-labelledby="modalEditarSaldoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-umss-navy text-white">
                <h5 class="modal-title" id="modalEditarSaldoLabel"><i class="fas fa-edit mr-2"></i> Editar Saldo de Tarjeta</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('usuarios.editarSaldo', $tarjeta->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres recargar ' + document.getElementById('nuevo_saldo').value + ' Bs.?');">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-info shadow-sm mb-4">
                        <i class="fas fa-info-circle mr-2"></i> <strong>Modificación directa:</strong> Esta opción añadirá el monto ingresado al saldo actual.
                    </div>

                    <div class="form-group text-center mb-4">
                        <label class="text-muted d-block">Saldo Actual</label>
                        <h3 class="text-success font-weight-bold">Bs. {{ number_format($tarjeta->saldo, 2) }}</h3>
                    </div>

                    <div class="form-group">
                        <label for="nuevo_saldo" class="font-weight-bold">Monto a recargar (Bs.) *</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light"><i class="fas fa-money-bill-wave text-success"></i></span>
                            </div>
                            <input type="number" name="nuevo_saldo" id="nuevo_saldo" class="form-control text-center font-weight-bold" 
                                   value="" min="0" max="5000" step="0.5" required>
                        </div>
                        <small class="form-text text-muted mt-2">Ingresa el monto que deseas sumar al saldo actual.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Guardar Nuevo Saldo</button>
                </div>
            </form>
        </div>
    </div>
</div>
