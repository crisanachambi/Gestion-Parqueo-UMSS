<div class="panel panel-default" id="panel-rfid">
   <div class="panel-body text-center">
      <p class="text-uppercase text-muted text-bold pv-small">ESCANEAR TARJETA RFID</p>

      <div class="form-group my-lg">
         <input type="text"
                name="codigo_rfid"
                id="input_rfid"
                class="form-control input-lg text-center font-monospace"
                placeholder="Acerque la tarjeta..."
                value="{{ old('codigo_rfid') }}"
                autocomplete="off"
                autofocus>
      </div>

      <small class="text-muted block mb">Presione Enter o acerque la tarjeta al lector</small>

      {{-- Solo para pruebas: tarjetas reales del seeder.
           Quitar antes de la entrega final. --}}
      @if(config('app.debug'))
         <div class="btn-group mt-2">
            <span class="small text-muted mr-2">Demo:</span>
            @foreach($tarjetasDemo ?? [] as $codigo)
               <button type="button" class="btn btn-xs btn-default ml-1"
                       onclick="simularLectura('{{ $codigo }}')">{{ $codigo }}</button>
            @endforeach
         </div>
      @endif
   </div>
</div>