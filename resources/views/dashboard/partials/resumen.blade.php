<!-- TARJETAS PRINCIPALES -->
<div class="row">
   <div class="col-xl-3 col-sm-6">
      <div class="card animated fadeInDownShort">
         <div class="card-body bg-success rounded-top">
            <div class="d-flex align-items-center">
               <div>
                  <p class="mb0">Espacios Libres</p>
                  <h3 class="m0">{{ $espaciosLibres }}</h3>
               </div>
               <div class="ml-auto"><em class="fa fa-parking fa-2x"></em></div>
            </div>
         </div>
      </div>
   </div>

   <div class="col-xl-3 col-sm-6">
      <div class="card animated fadeInDownShort">
         <div class="card-body bg-danger rounded-top">
            <div class="d-flex align-items-center">
               <div>
                  <p class="mb0">Espacios Ocupados</p>
                  <h3 class="m0">{{ $espaciosOcupados }}</h3>
               </div>
               <div class="ml-auto"><em class="fa fa-car-side fa-2x"></em></div>
            </div>
         </div>
      </div>
   </div>

   <div class="col-xl-3 col-sm-6">
      <div class="card animated fadeInDownShort">
         <div class="card-body bg-warning rounded-top">
            <div class="d-flex align-items-center">
               <div>
                  <p class="mb0">Ingresos de Hoy</p>
                  <h3 class="m0">Bs. {{ number_format($ingresosHoy, 2) }}</h3>
               </div>
               <div class="ml-auto"><em class="fa fa-dollar-sign fa-2x"></em></div>
            </div>
         </div>
      </div>
   </div>

   <div class="col-xl-3 col-sm-6">
      <div class="card animated fadeInDownShort">
         <div class="card-body bg-info rounded-top">
            <div class="d-flex align-items-center">
               <div>
                  <p class="mb0">Vehículos Hoy</p>
                  <h3 class="m0">{{ $vehiculosHoy }}</h3>
               </div>
               <div class="ml-auto"><em class="fa fa-car fa-2x"></em></div>
            </div>
         </div>
      </div>
   </div>
</div>