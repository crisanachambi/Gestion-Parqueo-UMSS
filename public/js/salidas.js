let ingresoActual = null;
let modoCobroFinal = 'efectivo';

const dinero = valor => Number(valor || 0).toFixed(2);

function abrirModalSalida(id) {
   ingresoActual = null;
   fetch(`/salidas/${id}/preview`, { headers: { Accept: 'application/json' } })
      .then(async res => {
         const data = await res.json();
         if (!res.ok) throw new Error(data.message || 'No se pudo preparar la salida.');
         return data;
      })
      .then(data => {
         ingresoActual = data;
         document.getElementById('modal-placa').innerText = data.placa;
         document.getElementById('modal-titular').innerText = data.titular;
         document.getElementById('modal-espacio').innerText = `Espacio ${data.espacio}`;
         document.getElementById('modal-hora-entrada').innerText = data.hora_entrada;
         document.getElementById('modal-hora-salida').innerText = data.hora_salida;
         document.getElementById('modal-tiempo-total').innerText = data.tiempo_total;
         document.getElementById('modal-periodos-cobrados').innerText = `${data.periodos} × ${data.tarifa_unitaria} Bs`;
         document.getElementById('btn-monto').innerText = data.monto_total;
         document.querySelectorAll('.modal-monto-total').forEach(el => el.innerText = data.monto_total);
         document.getElementById('modal-icono-vehiculo').className = data.tipo === 'auto' ? 'fa fa-car fa-2x text-danger' : 'fa fa-motorcycle fa-2x text-info';
         ocultarTodosLosEscenarios();

         if (data.es_visitante) {
            modoCobroFinal = 'efectivo';
            document.getElementById('escenario-efectivo').classList.remove('d-none');
            document.getElementById('btnConfirmarSalida').classList.remove('d-none');
         } else if (Number(data.saldo_disponible) < Number(data.monto_total)) {
            modoCobroFinal = 'efectivo';
            document.getElementById('escenario-saldo-insuficiente').classList.remove('d-none');
            document.getElementById('modal-saldo-disponible').innerText = dinero(data.saldo_disponible);
            document.getElementById('modal-saldo-faltante').innerText = dinero(data.monto_total - data.saldo_disponible);
            document.getElementById('btn-ir-recargar').href = `/recargas?tarjeta=${data.tarjeta_id}`;
            document.getElementById('btnConfirmarSalida').classList.remove('d-none');
         } else {
            modoCobroFinal = 'tarjeta';
            document.getElementById('escenario-rfid-lectura').classList.remove('d-none');
            document.getElementById('modal-rfid-saldo-actual').innerText = `${dinero(data.saldo_disponible)} Bs`;
            document.getElementById('modal-rfid-monto-esperado').innerText = `${data.monto_total} Bs`;
            reiniciarLecturaRfid();
         }
         $('#modalConfirmarSalida').modal('show');
      })
      .catch(error => alert(error.message));
}

function ocultarTodosLosEscenarios() {
   ['escenario-efectivo', 'escenario-saldo-insuficiente', 'escenario-rfid-lectura'].forEach(id => document.getElementById(id).classList.add('d-none'));
}

function mostrarSubEstadoRfid(estado) {
   ['esperando', 'verificado', 'error'].forEach(nombre => document.getElementById(`rfid-estado-${nombre}`).classList.toggle('d-none', nombre !== estado));
}

function reiniciarLecturaRfid() {
   mostrarSubEstadoRfid('esperando');
   const input = document.getElementById('input-rfid-salida');
   input.value = '';
   document.getElementById('btnConfirmarSalida').classList.add('d-none');
   setTimeout(() => input.focus(), 150);
}

function validarTarjetaRfid() {
   if (!ingresoActual) return;
   const codigo = document.getElementById('input-rfid-salida').value.trim();
   if (!codigo) return;
   if (codigo !== ingresoActual.codigo_rfid) {
      mostrarSubEstadoRfid('error');
      document.getElementById('btnConfirmarSalida').classList.add('d-none');
      return;
   }
   document.getElementById('modal-rfid-codigo').innerText = codigo;
   document.getElementById('rfid-saldo-previo').innerText = dinero(ingresoActual.saldo_disponible);
   document.getElementById('rfid-saldo-nuevo').innerText = dinero(ingresoActual.saldo_disponible - ingresoActual.monto_total);
   document.getElementById('rfid-monto-descuento').innerText = ingresoActual.monto_total;
   mostrarSubEstadoRfid('verificado');
   document.getElementById('btnConfirmarSalida').classList.remove('d-none');
}

function forzarCobroEfectivo() {
   modoCobroFinal = 'efectivo';
   ocultarTodosLosEscenarios();
   document.getElementById('escenario-efectivo').classList.remove('d-none');
   document.getElementById('btnConfirmarSalida').classList.remove('d-none');
}

async function ejecutarSalida() {
   if (!ingresoActual) return;
   const boton = document.getElementById('btnConfirmarSalida');
   boton.disabled = true;

   try {
      const res = await fetch(`/salidas/${ingresoActual.registro_id}`, {
         method: 'POST',
         headers: { 
            'Content-Type': 'application/json', 
            'Accept': 'application/json', 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
         },
         body: JSON.stringify({ 
            metodo_pago: modoCobroFinal,
            codigo_rfid: document.getElementById('input-rfid-salida')?.value.trim() || '' 
         })
      });
      
      const contenido = await res.text();
      let data = {};
      try {
         data = contenido ? JSON.parse(contenido) : {};
      } catch (parseError) {
         throw new Error(`El servidor respondió con HTTP ${res.status}, pero no devolvió JSON.`);
      }
      
      if (!res.ok) {
         throw new Error(data.message || data.errors?.metodo_pago?.[0] || `No se pudo registrar la salida (HTTP ${res.status}).`);
      }
      
      window.location.reload();
   } catch (error) { 
      boton.disabled = false; 
      alert(error.message || 'No se pudo registrar la salida.');
   }
}
