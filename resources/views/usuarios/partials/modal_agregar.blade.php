<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-umss-navy text-white">
                <h5 class="modal-title" id="modalAgregarUsuarioLabel"><i class="fas fa-user-plus mr-2"></i> Registrar Nuevo Usuario</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <h6 class="font-weight-bold border-bottom pb-2 mb-3 text-umss"><i class="fas fa-user mr-1"></i> Datos Personales</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nombre">Nombre(s) *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="100">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="apellido">Apellidos</label>
                            <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido') }}" maxlength="100">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="ci">Cédula de Identidad (C.I.) *</label>
                            <input type="text" name="ci" id="ci" class="form-control" value="{{ old('ci') }}" required maxlength="20">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="telefono">Teléfono/Celular</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="20">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="categoria">Categoría</label>
                            <select name="categoria" id="categoria" class="form-control">
                                <option value="estudiante" {{ old('categoria') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="docente" {{ old('categoria') == 'docente' ? 'selected' : '' }}>Docente</option>
                                <option value="administrativo" {{ old('categoria') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                                <option value="visitante" {{ old('categoria') == 'visitante' ? 'selected' : '' }}>Visitante</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="font-weight-bold border-bottom pb-2 mt-4 mb-3 text-umss"><i class="fas fa-car mr-1"></i> Datos del Vehículo Principal</h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="placa">Placa *</label>
                            <input type="text" name="vehiculos[0][placa]" id="placa" class="form-control text-uppercase" value="{{ old('vehiculos.0.placa') }}" required maxlength="15">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="tipo">Tipo *</label>
                            <select name="vehiculos[0][tipo]" id="tipo" class="form-control" required>
                                <option value="auto">Auto</option>
                                <option value="moto">Moto</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="marca">Marca</label>
                            <input type="text" name="vehiculos[0][marca]" id="marca" class="form-control" value="{{ old('vehiculos.0.marca') }}" maxlength="60">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="color">Color</label>
                            <input type="text" name="vehiculos[0][color]" id="color" class="form-control" value="{{ old('vehiculos.0.color') }}" maxlength="40">
                        </div>
                    </div>

                    <h6 class="font-weight-bold border-bottom pb-2 mt-4 mb-3 text-umss"><i class="fas fa-id-badge mr-1"></i> Asignación de Tarjeta RFID</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="codigo_rfid">UID Tarjeta RFID *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-wifi"></i></span>
                                </div>
                                <input type="text" name="codigo_rfid" id="codigo_rfid" class="form-control" value="{{ old('codigo_rfid') }}" required maxlength="32" placeholder="Escanee la tarjeta...">
                            </div>
                            <small class="form-text text-muted">Asegúrese de que el lector esté activo y posicionado en este campo antes de escanear.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="saldo_inicial">Saldo Inicial (Bs.)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Bs.</span>
                                </div>
                                <input type="number" name="saldo_inicial" id="saldo_inicial" class="form-control" min="0" max="1000" step="0.5" value="{{ old('saldo_inicial', 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Guardar Usuario y Tarjeta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rfidInput = document.getElementById('codigo_rfid');
        let pollingInterval = null;

        // --- 1. Polling para el escáner ESP32 ---
        function pollUltimaTarjeta() {
            fetch('{{ route("usuarios.ultimaTarjeta") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.codigo) {
                        rfidInput.value = data.codigo;
                        rfidInput.classList.add('is-valid');
                        setTimeout(() => rfidInput.classList.remove('is-valid'), 2000);
                    }
                })
                .catch(err => console.error('Error polling RFID:', err));
        }

        $('#modalAgregarUsuario').on('shown.bs.modal', function () {
            // Empezar a consultar cada 2 segundos cuando el modal está abierto
            if (!pollingInterval) {
                pollingInterval = setInterval(pollUltimaTarjeta, 1500);
            }
        });

        $('#modalAgregarUsuario').on('hidden.bs.modal', function () {
            // Detener la consulta cuando se cierra
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        });

        // --- 2. Listener para escáneres USB (emuladores de teclado) ---
        let rfidBuffer = '';
        let lastKeyTime = Date.now();
        
        window.addEventListener('keydown', function(e) {
            const modal = document.getElementById('modalAgregarUsuario');
            if (!modal || !modal.classList.contains('show')) return;

            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 50) {
                rfidBuffer = '';
            }
            lastKeyTime = currentTime;

            if (e.key.length === 1) {
                rfidBuffer += e.key;
            }

            if (e.key === 'Enter' && rfidBuffer.length >= 5) {
                e.preventDefault(); 
                rfidInput.value = rfidBuffer;
                
                if (e.target.tagName === 'INPUT' && e.target.id !== 'codigo_rfid' && e.target.type === 'text') {
                    const val = e.target.value;
                    if (val.endsWith(rfidBuffer)) {
                        e.target.value = val.slice(0, -rfidBuffer.length);
                    }
                }
                rfidBuffer = '';
                rfidInput.classList.add('is-valid');
                setTimeout(() => rfidInput.classList.remove('is-valid'), 2000);
            }
        });
    });
</script>
@endpush
