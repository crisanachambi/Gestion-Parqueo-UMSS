<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-umss-navy text-white">
                <h5 class="modal-title" id="modalEditarUsuarioLabel"><i class="fas fa-user-edit mr-2"></i> Editar Datos de Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3 text-umss"><i class="fas fa-user mr-1"></i> Datos Personales</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="edit_nombre">Nombre(s) *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" value="{{ old('nombre', $usuario->nombre) }}" required maxlength="100">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="edit_apellido">Apellidos</label>
                            <input type="text" name="apellido" id="edit_apellido" class="form-control" value="{{ old('apellido', $usuario->apellido) }}" maxlength="100">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Cédula de Identidad (C.I.)</label>
                            <input type="text" class="form-control" value="{{ $usuario->ci }}" disabled>
                            <small class="form-text text-muted">El C.I. no se puede modificar.</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="edit_telefono">Teléfono/Celular</label>
                            <input type="text" name="telefono" id="edit_telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}" maxlength="20">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="edit_categoria">Categoría</label>
                            <select name="categoria" id="edit_categoria" class="form-control">
                                <option value="estudiante" {{ old('categoria', $usuario->categoria) == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="docente" {{ old('categoria', $usuario->categoria) == 'docente' ? 'selected' : '' }}>Docente</option>
                                <option value="administrativo" {{ old('categoria', $usuario->categoria) == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                                <option value="visitante" {{ old('categoria', $usuario->categoria) == 'visitante' ? 'selected' : '' }}>Visitante</option>
                            </select>
                        </div>
                    </div>
                    <h6 class="font-weight-bold border-bottom pb-2 mt-4 mb-3 text-umss"><i class="fas fa-car mr-1"></i> Datos del Vehículo Principal</h6>
                    @php($vehiculo = $usuario->vehiculos->first())
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="edit_placa">Placa</label>
                            <input type="text" name="vehiculos[0][placa]" id="edit_placa" class="form-control text-uppercase" value="{{ old('vehiculos.0.placa', $vehiculo->placa ?? '') }}" maxlength="15">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="edit_tipo">Tipo</label>
                            <select name="vehiculos[0][tipo]" id="edit_tipo" class="form-control">
                                <option value="auto" {{ old('vehiculos.0.tipo', $vehiculo->tipo ?? '') == 'auto' ? 'selected' : '' }}>Auto</option>
                                <option value="moto" {{ old('vehiculos.0.tipo', $vehiculo->tipo ?? '') == 'moto' ? 'selected' : '' }}>Moto</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="edit_marca">Marca</label>
                            <input type="text" name="vehiculos[0][marca]" id="edit_marca" class="form-control" value="{{ old('vehiculos.0.marca', $vehiculo->marca ?? '') }}" maxlength="60">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="edit_color">Color</label>
                            <input type="text" name="vehiculos[0][color]" id="edit_color" class="form-control" value="{{ old('vehiculos.0.color', $vehiculo->color ?? '') }}" maxlength="40">
                        </div>
                    </div>

                    <h6 class="font-weight-bold border-bottom pb-2 mt-4 mb-3 text-umss"><i class="fas fa-id-badge mr-1"></i> Tarjeta RFID</h6>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="edit_codigo_rfid">UID Tarjeta RFID</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-wifi"></i></span>
                                </div>
                                <input type="text" name="codigo_rfid" id="edit_codigo_rfid" class="form-control" value="{{ old('codigo_rfid', $usuario->tarjetaActual->codigo_rfid ?? '') }}" maxlength="32" placeholder="Escanee la nueva tarjeta si desea reemplazarla...">
                            </div>
                            <small class="form-text text-muted">Atención: Si cambia el UID de la tarjeta, el saldo se reiniciará a Bs. 0.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rfidInputEdit = document.getElementById('edit_codigo_rfid');
        if (!rfidInputEdit) return;
        
        let pollingIntervalEdit = null;

        function pollUltimaTarjetaEdit() {
            fetch('{{ route("usuarios.ultimaTarjeta") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.codigo) {
                        rfidInputEdit.value = data.codigo;
                        rfidInputEdit.classList.add('is-valid');
                        setTimeout(() => rfidInputEdit.classList.remove('is-valid'), 2000);
                    }
                })
                .catch(err => console.error('Error polling RFID:', err));
        }

        $('#modalEditarUsuario').on('shown.bs.modal', function () {
            if (!pollingIntervalEdit) {
                pollingIntervalEdit = setInterval(pollUltimaTarjetaEdit, 1500);
            }
        });

        $('#modalEditarUsuario').on('hidden.bs.modal', function () {
            if (pollingIntervalEdit) {
                clearInterval(pollingIntervalEdit);
                pollingIntervalEdit = null;
            }
        });

        let rfidBufferEdit = '';
        let lastKeyTimeEdit = Date.now();
        
        window.addEventListener('keydown', function(e) {
            const modal = document.getElementById('modalEditarUsuario');
            if (!modal || !modal.classList.contains('show')) return;

            const currentTime = Date.now();
            if (currentTime - lastKeyTimeEdit > 50) {
                rfidBufferEdit = '';
            }
            lastKeyTimeEdit = currentTime;

            if (e.key.length === 1) {
                rfidBufferEdit += e.key;
            }

            if (e.key === 'Enter' && rfidBufferEdit.length >= 5) {
                e.preventDefault(); 
                rfidInputEdit.value = rfidBufferEdit;
                
                if (e.target.tagName === 'INPUT' && e.target.id !== 'edit_codigo_rfid' && e.target.type === 'text') {
                    const val = e.target.value;
                    if (val.endsWith(rfidBufferEdit)) {
                        e.target.value = val.slice(0, -rfidBufferEdit.length);
                    }
                }
                rfidBufferEdit = '';
                rfidInputEdit.classList.add('is-valid');
                setTimeout(() => rfidInputEdit.classList.remove('is-valid'), 2000);
            }
        });
    });
</script>
@endpush
