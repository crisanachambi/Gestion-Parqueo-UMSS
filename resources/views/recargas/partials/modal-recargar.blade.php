<div class="modal fade" id="modalRecargarTarjeta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            
            <!-- Cabecera -->
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark">Recargar tarjeta</h5>
                    <small class="text-muted" id="modalNombreTitular">Carlos Andrés Mamani Flores</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('recargas.store') }}" method="POST" id="formRecarga">
                @csrf
                <input type="hidden" name="usuario_id" id="modalUsuarioId">
                
                <div class="modal-body pt-3">
                    <!-- Resumen RFID y Saldo Actual -->
                    <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 0.75rem;">Tarjeta RFID</small>
                            <strong class="h6 text-dark mb-0" id="modalRfidCode">A3F92B11</strong>
                        </div>
                        <div class="text-right">
                            <small class="text-uppercase text-muted font-weight-bold d-block" style="font-size: 0.75rem;">Saldo Actual</small>
                            <span class="h4 font-weight-bold text-success mb-0"><span id="modalSaldoActual">28</span> Bs</span>
                        </div>
                    </div>

                    <!-- Input Monto -->
                    <div class="form-group mb-3">
                        <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Monto a recargar (BS)</label>
                        <input type="number" step="1" min="1" name="monto" id="montoInput" class="form-control form-control-lg text-center font-weight-bold" placeholder="0" autocomplete="off" required>
                    </div>

                    <!-- Botones de Montos Frecuentes -->
                    <label class="text-uppercase text-muted font-weight-bold d-block mb-2" style="font-size: 0.75rem;">Montos Frecuentes</label>
                    <div class="row no-gutters mb-3">
                        <div class="col-3 pr-1"><button type="button" class="btn btn-outline-secondary btn-block btn-monto font-weight-bold" data-monto="10">10 Bs</button></div>
                        <div class="col-3 px-1"><button type="button" class="btn btn-outline-secondary btn-block btn-monto font-weight-bold" data-monto="20">20 Bs</button></div>
                        <div class="col-3 px-1"><button type="button" class="btn btn-outline-secondary btn-block btn-monto font-weight-bold" data-monto="50">50 Bs</button></div>
                        <div class="col-3 pl-1"><button type="button" class="btn btn-outline-secondary btn-block btn-monto font-weight-bold" data-monto="100">100 Bs</button></div>
                    </div>

                    <!-- Vista Previa de Suma -->
                    <div id="vistaPreviaBox" class="p-3 rounded border border-success mb-3" style="background-color: #e6f7f0; display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted font-weight-bold" style="font-size: 0.85rem;">Vista previa</span>
                            <span class="h4 font-weight-bold text-success mb-0">= <span id="totalNuevoSaldo">0</span> Bs</span>
                        </div>
                        <small class="text-muted"><span id="calcSaldoActual">0</span> Bs + <span id="calcMontoIngresado">0</span> Bs</small>
                    </div>
                </div>

                <!-- Botones Acción -->
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnConfirmar" class="btn btn-success px-4 font-weight-bold" disabled>
                        <i class="fas fa-check mr-1"></i> Confirmar recarga de <span id="btnMontoText">0</span> Bs
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>