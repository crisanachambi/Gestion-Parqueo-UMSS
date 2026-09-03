<div id="escenario-rfid-lectura" class="d-none">
   <!-- Resumen del cobro -->
   <div class="p-3 rounded d-flex justify-content-between align-items-center box-total-cobro mb-3">
      <div>
         <span class="font-weight-bold text-success d-block">Total a cobrar</span>
         <small id="modal-tipo-pago-rfid" class="text-success font-weight-bold">💳 Descuento de saldo RFID</small>
      </div>
      <div class="h2 font-weight-bold text-success mb-0">
         <span class="modal-monto-total">0</span> Bs
      </div>
   </div>

   <!-- Sub-estado 3.1: Esperando tarjeta -->
   <div id="rfid-estado-esperando" class="card-rfid-state bg-soft-blue text-center p-4 rounded mb-3">
      <em class="fa fa-wifi fa-2x text-primary mb-2 d-block icon-pulse"></em>
      <span class="font-weight-bold text-primary d-block h6 mb-0">Acerque la tarjeta para autorizar el cobro</span>
      <label for="input-rfid-salida" class="sr-only">Código RFID</label>
      <input id="input-rfid-salida" type="text" class="form-control mt-3 text-center"
             autocomplete="off" placeholder="Escanee o escriba el código RFID">
      <small class="text-muted d-block mt-1">El código leído se muestra aquí para verificarlo.</small>
      <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top extra-small text-muted">
         <span>Saldo actual: <strong id="modal-rfid-saldo-actual" class="text-dark">0 Bs</strong></span>
         <span>Monto: <strong id="modal-rfid-monto-esperado" class="text-dark">0 Bs</strong></span>
      </div>
   </div>

   <!-- Sub-estado 3.2: Tarjeta Verificada -->
   <div id="rfid-estado-verificado" class="card-rfid-state bg-soft-success text-center p-3 rounded mb-3 d-none">
      <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle mb-2 icon-check-circle">
         <em class="fa fa-check"></em>
      </div>
      <h6 class="font-weight-bold text-success mb-1">Titular verificado</h6>
      <small id="modal-rfid-codigo" class="text-muted d-block mb-2">--</small>
      
      <div class="bg-white p-2 rounded d-flex justify-content-between align-items-center font-weight-bold border">
         <span class="text-dark small"><span id="rfid-saldo-previo">0</span> Bs ➔ <span id="rfid-saldo-nuevo">0</span> Bs</span>
         <span class="text-danger">-<span id="rfid-monto-descuento">0</span> Bs</span>
      </div>
   </div>

   <!-- Sub-estado 3.3: Tarjeta Incorrecta -->
   <div id="rfid-estado-error" class="card-rfid-state bg-soft-danger text-center p-3 rounded mb-3 d-none">
      <em class="fa fa-exclamation-triangle fa-2x text-danger mb-2 d-block"></em>
      <h6 class="font-weight-bold text-danger mb-1">No es la tarjeta del titular</h6>
      <small class="text-danger d-block mb-3">Vuelva a intentar con la tarjeta correspondiente</small>
      <div class="d-flex justify-content-center">
         <button type="button" class="btn btn-sm btn-outline-danger mr-2 font-weight-bold px-3" onclick="reiniciarLecturaRfid()">
            Reintentar
         </button>
         <button type="button" class="btn btn-sm btn-white border font-weight-bold text-dark px-3" onclick="forzarCobroEfectivo()">
            Cobrar efectivo
         </button>
      </div>
   </div>
</div>
