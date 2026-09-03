document.addEventListener('DOMContentLoaded', function () {
   const inputRfid = document.getElementById('input_rfid');
   const formIngreso = document.getElementById('form-ingreso');
   const inputPlaca = document.getElementById('input_placa');

   // 1. Enfoque automático y detección de lectura RFID
   if (inputRfid) {
      inputRfid.focus();
      inputRfid.addEventListener('keydown', function (e) {
         if (e.key === 'Enter') {
            e.preventDefault();
            const rfidVal = this.value.trim();
            if (rfidVal.length > 0) {
               consultarUsuarioRfid(rfidVal);
            }
         }
      });
   }

   // 2. Validación en tiempo real del input de Placa (Modo Visitante)
   if (inputPlaca) {
      inputPlaca.addEventListener('input', function () {
         this.value = this.value.toUpperCase();
         validarBotonConfirmar();
      });
   }

   // 3. Procesamiento del envío por AJAX
   if (formIngreso) {
      formIngreso.addEventListener('submit', function (e) {
         e.preventDefault();
         guardarIngreso();
      });
   }
});

/**
 * Alterna entre el modo 'rfid' y 'visitante'
 */
function cambiarModo(modo) {
   document.getElementById('tipo_ingreso').value = modo;
   
   const btnRfid = document.getElementById('btn-tab-rfid');
   const btnVisitante = document.getElementById('btn-tab-visitante');
   const panelRfid = document.getElementById('panel-rfid');
   const panelUser = document.getElementById('panel-info-usuario');
   const panelVisitante = document.getElementById('panel-visitante');
   const emptyState = document.getElementById('empty-space-state');
   const gridContainer = document.getElementById('grid-spaces-container');

   resetSeleccion();

   if (modo === 'rfid') {
      btnRfid.classList.add('active');
      btnVisitante.classList.remove('active');

      panelRfid.classList.remove('hidden');
      panelUser.classList.add('hidden');
      panelVisitante.classList.add('hidden');

      // Estado de espera RFID
      emptyState.classList.remove('hidden');
      gridContainer.classList.add('hidden');

      const inputRfid = document.getElementById('input_rfid');
      if (inputRfid) {
         inputRfid.value = '';
         inputRfid.focus();
      }
   } else {
      btnVisitante.classList.add('active');
      btnRfid.classList.remove('active');

      panelRfid.classList.add('hidden');
      panelUser.classList.add('hidden');
      panelVisitante.classList.remove('hidden');

      // En modo visitante se despliega la grilla inmediatamente
      emptyState.classList.add('hidden');
      gridContainer.classList.remove('hidden');

      // Por defecto selecciona 'auto'
      seleccionarTipoVehiculo('auto');
      
      const inputPlaca = document.getElementById('input_placa');
      if (inputPlaca) inputPlaca.focus();
   }
}

/**
 * Consulta la API de Laravel para verificar la tarjeta RFID
 */
function consultarUsuarioRfid(codigoRfid) {
   fetch(`/ingresos/rfid/buscar?codigo=${codigoRfid}`)
      .then(response => {
         if (!response.ok) throw new Error('Tarjeta no encontrada');
         return response.json();
      })
      .then(data => {
         // Cargar datos en la ficha del abonado
         document.getElementById('usuario-nombre').innerText = data.nombre;
         document.getElementById('usuario-ci').innerText = `CI: ${data.ci}`;
         document.getElementById('usuario-saldo').innerHTML = `${data.saldo} <small class="text-success font-weight-bold">Bs</small>`;
         document.getElementById('badge-placa').innerText = data.vehiculo.placa;
         document.getElementById('texto-tipo-vehiculo').innerText = data.vehiculo.tipo;

         // Mostrar ficha y activar grilla
         document.getElementById('panel-rfid').classList.add('hidden');
         document.getElementById('panel-info-usuario').classList.remove('hidden');
         document.getElementById('empty-space-state').classList.add('hidden');
         document.getElementById('grid-spaces-container').classList.remove('hidden');

         // Filtrar grilla según la movilidad registrada en la tarjeta
         filtrarEspaciosPorTipo(data.vehiculo.tipo.toLowerCase());
      })
      .catch(error => {
         alert('La tarjeta RFID escaneada no está registrada o se encuentra inactiva.');
         document.getElementById('input_rfid').value = '';
         document.getElementById('input_rfid').focus();
      });
}

/**
 * Cambia el tipo de vehículo seleccionado en Modo Visitante
 */
function seleccionarTipoVehiculo(tipo) {
   document.getElementById('tipo_vehiculo_selected').value = tipo;   // ← nueva

   const btnAuto = document.getElementById('btn-tipo-auto');
   const btnMoto = document.getElementById('btn-tipo-moto');

   if (tipo === 'auto') {
      btnAuto.classList.add('active');
      btnMoto.classList.remove('active');
   } else {
      btnMoto.classList.add('active');
      btnAuto.classList.remove('active');
   }

   filtrarEspaciosPorTipo(tipo);
}

/**
 * Oculta/Muestra casilleros según la movilidad (Auto/Moto)
 */
function filtrarEspaciosPorTipo(tipo) {
   document.getElementById('label-tipo-vehiculo').innerText = `solo ${tipo}s`;
   let libresContador = 0;

   document.querySelectorAll('.espacio-item').forEach(el => {
      const tipoEspacio = el.getAttribute('data-tipo');
      const estadoEspacio = el.getAttribute('data-estado');

      if (tipoEspacio === tipo) {
         el.classList.remove('hidden');
         if (estadoEspacio === 'libre') libresContador++;
      } else {
         el.classList.add('hidden');
      }
   });

   document.getElementById('badge-disponibles').innerText = `${libresContador} disponibles`;
   document.getElementById('texto-resumen-libres').innerText = libresContador;
   resetSeleccion();
}

/**
 * Selecciona una casilla libre en la grilla
 */
function seleccionarEspacio(id, numero, elemento) {
   // Desmarcar selección previa
   document.querySelectorAll('.space-card').forEach(card => card.classList.remove('selected'));

   // Marcar nuevo espacio
   elemento.classList.add('selected');
   document.getElementById('espacio_id_selected').value = id;

   // Actualizar Banner y Botón de Confirmación
   document.getElementById('banner-espacio-seleccionado').classList.remove('hidden');
   document.getElementById('texto-numero-espacio').innerText = numero;
   document.getElementById('btn-confirmar-texto').innerText = `✓ Confirmar ingreso → Esp. ${numero}`;

   validarBotonConfirmar();
}

/**
 * Limpia el espacio seleccionado
 */
function resetSeleccion() {
   document.getElementById('espacio_id_selected').value = '';
   document.querySelectorAll('.space-card').forEach(card => card.classList.remove('selected'));
   document.getElementById('banner-espacio-seleccionado').classList.add('hidden');
   document.getElementById('btn-confirmar-texto').innerText = '✓ Confirmar ingreso';
   
   const btnConfirmar = document.getElementById('btn-confirmar');
   btnConfirmar.setAttribute('disabled', 'disabled');
}

/**
 * Habilita el botón de confirmación si se cumplen las condiciones
 */
function validarBotonConfirmar() {
   const modo = document.getElementById('tipo_ingreso').value;
   const espacioId = document.getElementById('espacio_id_selected').value;
   const btnConfirmar = document.getElementById('btn-confirmar');

   if (!espacioId) {
      btnConfirmar.setAttribute('disabled', 'disabled');
      return;
   }

   if (modo === 'visitante') {
      const placa = document.getElementById('input_placa').value.trim();
      if (placa.length < 3) {
         btnConfirmar.setAttribute('disabled', 'disabled');
         return;
      }
   }

   btnConfirmar.removeAttribute('disabled');
}

/**
 * Envía el formulario vía Fetch API a Laravel y muestra pantalla de éxito
 */
function guardarIngreso() {
   const form = document.getElementById('form-ingreso');
   const formData = new FormData(form);

   fetch(form.action, {
      method: 'POST',
      headers: {
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
         'Accept': 'application/json'
      },
      body: formData
   })
   .then(response => response.json())
   .then(data => {
      if (data.success) {
         mostrarPantallaExito(data.espacio_numero);
      } else {
         alert(data.message || 'Ocurrió un error al registrar el ingreso.');
      }
   })
   .catch(error => alert('Error de conexión con el servidor.'));
}

/**
 * Muestra la alerta/pantalla de éxito y resetea el módulo
 */
function mostrarPantallaExito(espacioNumero) {
   document.getElementById('contenedor-principal').classList.add('hidden');
   document.getElementById('pantalla-exito').classList.remove('hidden');
   document.getElementById('texto-espacio-confirmado').innerText = `Espacio ${espacioNumero}`;

   setTimeout(() => {
      document.getElementById('pantalla-exito').classList.add('hidden');
      document.getElementById('contenedor-principal').classList.remove('hidden');
      cambiarModo('rfid');
      window.location.reload(); // Recarga para actualizar estados desde BD
   }, 2500);
}

/**
 * Botones demo: simula el paso de una tarjeta por el lector.
 * Solo para pruebas sin hardware conectado.
 */
function simularLectura(codigo) {
   const input = document.getElementById('input_rfid');
   input.value = codigo;
   consultarUsuarioRfid(codigo);
}