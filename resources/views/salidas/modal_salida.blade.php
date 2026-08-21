<div class="modal fade" id="modalConfirmarSalida" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="true" data-keyboard="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg modal-rounded">
         
         <!-- Header -->
         <div class="modal-header border-0 pb-0">
            <h5 class="modal-title font-weight-bold text-dark">Confirmar salida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
         </div>

         <!-- Body -->
         <div class="modal-body pt-2">
            
            <!-- Datos del Vehículo / Titular / Espacio (Compartido) -->
            <div class="d-flex align-items-center mb-3">
               <div class="p-3 mr-3 bg-light rounded text-center box-icono-vehiculo">
                  <em id="modal-icono-vehiculo" class="fa fa-car fa-2x text-danger"></em>
               </div>
               <div>
                  <h3 id="modal-placa" class="font-weight-bold mb-0 text-dark">--</h3>
                  <div id="modal-titular" class="text-muted small">--</div>
                  <div id="modal-espacio" class="text-muted small">--</div>
               </div>
            </div>

            <!-- Tiempos e Historial (Compartido) -->
            <div class="bg-light p-3 rounded mb-3">
               <div class="row text-center">
                  <div class="col-6 mb-2">
                     <span class="text-uppercase text-muted extra-small d-block">HORA DE ENTRADA</span>
                     <strong id="modal-hora-entrada" class="text-dark">--</strong>
                  </div>
                  <div class="col-6 mb-2">
                     <span class="text-uppercase text-muted extra-small d-block">HORA DE SALIDA</span>
                     <strong id="modal-hora-salida" class="text-dark">--</strong>
                  </div>
                  <div class="col-6">
                     <span class="text-uppercase text-muted extra-small d-block">TIEMPO TOTAL</span>
                     <strong id="modal-tiempo-total" class="text-dark">--</strong>
                  </div>
                  <div class="col-6">
                     <span class="text-uppercase text-muted extra-small d-block">PERIODOS COBRADOS</span>
                     <strong id="modal-periodos-cobrados" class="text-dark">--</strong>
                  </div>
               </div>
            </div>

            <!-- Vistas parciales integradas -->
            @include('salidas.escenarios.efectivo')
            @include('salidas.escenarios.saldo_insuficiente')
            @include('salidas.escenarios.rfid_lectura')

         </div>

         <!-- Footer -->
         <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold" data-dismiss="modal">
               Cancelar
            </button>
            <button type="button" 
                    id="btnConfirmarSalida" 
                    class="btn btn-success px-4 font-weight-bold" 
                    onclick="ejecutarSalida()">
               ✓ Confirmar salida · <span id="btn-monto">0</span> Bs
            </button>
         </div>

      </div>
   </div>
</div>
