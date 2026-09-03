<div class="panel panel-default hidden mb-3" id="panel-visitante">
   <div class="panel-body p-3">
      {{-- El valor lo actualiza seleccionarTipoVehiculo() al
        pulsar los botones de Auto o Moto. Sin este input,
        el tipo nunca viaja en el POST. --}}
   <input type="hidden" name="tipo_vehiculo" id="tipo_vehiculo_selected" value="auto">

   <!-- Aviso de cobro en efectivo -->
   <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-3" ...>
         <em class="fa fa-money-bill-alt mr-2 text-primary" style="font-size: 1.2rem;"></em>
         <span>El cobro se realizará en <strong>efectivo al momento de la salida</strong></span>
      </div>

      <!-- Placa del Vehículo -->
      <div class="form-group mb-3">
         <label class="control-label font-weight-bold text-dark small mb-1">PLACA DEL VEHÍCULO</label>
         <input type="text"
                name="placa"
                id="input_placa"
                class="form-control input-lg text-center font-weight-bold text-uppercase font-monospace"
                placeholder="1234-ABC"
                maxlength="10"
                autocomplete="off">
      </div>

      <!-- Selector de Tipo de Vehículo -->
      <div class="form-group mb-0">
         <label class="control-label font-weight-bold text-dark small mb-1">TIPO DE VEHÍCULO</label>
         <div class="row row-flush">
            <div class="col-xs-6 pr-1">
               <button type="button"
                       class="btn btn-block p-3 text-center btn-tipo-vehiculo active"
                       id="btn-tipo-auto"
                       onclick="seleccionarTipoVehiculo('auto')">
                  <em class="fa fa-car fa-lg mb-1 block text-danger"></em>
                  <strong class="block text-dark">Auto</strong>
                  <small class="text-muted block">{{ $parqueo->tarifa_auto ?? 4 }} Bs/periodo</small>
               </button>
            </div>
            <div class="col-xs-6 pl-1">
               <button type="button"
                       class="btn btn-block p-3 text-center btn-tipo-vehiculo"
                       id="btn-tipo-moto"
                       onclick="seleccionarTipoVehiculo('moto')">
                  <em class="fa fa-motorcycle fa-lg mb-1 block text-info"></em>
                  <strong class="block text-dark">Moto</strong>
                  <small class="text-muted block">{{ $parqueo->tarifa_moto ?? 1 }} Bs/periodo</small>
               </button>
            </div>
         </div>
      </div>

   </div>
</div>