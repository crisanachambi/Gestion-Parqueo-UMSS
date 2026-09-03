<div id="escenario-saldo-insuficiente" class="d-none">
   <!-- Resumen del cobro con RFID -->
   <div class="p-3 rounded d-flex justify-content-between align-items-center box-total-cobro mb-3">
      <div>
         <span class="font-weight-bold text-success d-block">Total a cobrar</span>
         <small class="text-success font-weight-bold">💳 Descuento de saldo RFID</small>
      </div>
      <div class="h2 font-weight-bold text-success mb-0">
         <span class="modal-monto-total">0</span> Bs
      </div>
   </div>

   <!-- Alerta de Saldo Insuficiente -->
   <div class="box-alerta-saldo p-3 rounded mb-3">
      <div class="text-danger font-weight-bold small mb-1">
         <em class="fa fa-exclamation-triangle mr-1"></em> 
         Saldo insuficiente (<span id="modal-saldo-disponible">0</span> Bs disponibles)
      </div>
      <div class="text-danger extra-small mb-3">
         Falta <span id="modal-saldo-faltante">0</span> Bs. Puede recargar la tarjeta o cobrar en efectivo.
      </div>
      <div class="d-flex">
         <a href="#" id="btn-ir-recargar" class="btn btn-sm btn-outline-warning text-dark bg-white font-weight-bold mr-2 border-warning">
            Ir a Recargar
         </a>
         <button type="button" class="btn btn-sm btn-white text-dark font-weight-bold border shadow-xs" onclick="forzarCobroEfectivo()">
            Cobrar en efectivo
         </button>
      </div>
   </div>
</div>