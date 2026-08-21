<div class="panel panel-default hidden mb-3" id="panel-info-usuario">
   <div class="panel-body p-3 card-usuario-rfid">
      
      <!-- Fila Superior: Nombre del Usuario y Saldo Disponible -->
      <div class="d-flex justify-content-between align-items-start mb-2">
         <div>
            <h4 class="m-0 font-weight-bold text-dark" id="usuario-nombre">
               {{ $usuario->nombre ?? 'Nombre del Usuario' }}
            </h4>
            <small class="text-muted block mt-1" id="usuario-ci">
               CI: {{ $usuario->ci ?? '--------' }}
            </small>
         </div>
         <div class="text-right">
            <span class="text-uppercase text-muted small font-weight-bold block">SALDO</span>
            <span class="h3 font-weight-bold text-success m-0" id="usuario-saldo">
               {{ $usuario->saldo ?? '0' }} <small class="text-success font-weight-bold">Bs</small>
            </span>
         </div>
      </div>

      <!-- Fila Inferior: Etiquetas de Vehículo, Placa y Tarifa -->
      <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top">
         <!-- Tipo de Vehículo -->
         <span class="badge badge-light text-dark p-2 font-weight-bold" id="badge-tipo-vehiculo">
            <em class="fa fa-car mr-1 text-danger" id="icono-tipo-vehiculo"></em>
            <span id="texto-tipo-vehiculo">Auto</span>
         </span>

         <!-- Placa Registrada -->
         <span class="badge badge-light text-dark p-2 font-weight-bold font-monospace" id="badge-placa">
            1234-ABC
         </span>

         <!-- Tarifa Aplicada -->
         <small class="text-muted ml-auto" id="texto-tarifa">
            Tarifa: 4 Bs/periodo
         </small>
      </div>

   </div>
</div>