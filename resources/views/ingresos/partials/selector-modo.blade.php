<div class="panel panel-default mb-3">
   <div class="panel-body p-2">
      <div class="row row-flush">
         <div class="col-xs-6 pr-1">
            <button type="button"
                    id="btn-tab-rfid"
                    class="btn btn-block p-3 text-center active"
                    onclick="cambiarModo('rfid')">
               <em class="fa fa-credit-card mr-2 text-primary"></em>
               <strong class="text-dark">Con tarjeta RFID</strong>
            </button>
         </div>
         <div class="col-xs-6 pl-1">
            <button type="button"
                    id="btn-tab-visitante"
                    class="btn btn-block p-3 text-center"
                    onclick="cambiarModo('visitante')">
               <em class="fa fa-user mr-2 text-warning"></em>
               <strong class="text-dark">Visitante sin tarjeta</strong>
            </button>
         </div>
      </div>
   </div>
</div>