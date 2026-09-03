@extends('layouts.app')

@section('title', 'Registrar Ingreso - ParkUMSS')

@push('styles')
   <link rel="stylesheet" href="{{ asset('css/ingresos.css') }}">
@endpush

@section('content')
<div class="container-fluid py-2">

   <!-- Encabezado Principal -->
   <div class="content-heading d-flex justify-content-between align-items-center mb-4">
      <div>
         <h3 class="m-0 font-weight-bold" style="color: #0b1b3d;">Registrar ingreso</h3>
         <small class="text-muted">Gestión de accesos y asignación de espacios en tiempo real</small>
      </div>
      <div>
         <span class="badge badge-pill border-0 px-3 py-2 font-weight-bold shadow-sm" style="background-color: #e6f7f0; color: #0d8a52; font-size: 0.85rem;">
            <i class="fas fa-parking mr-1"></i> {{ $parqueo->nombre }}
         </span>
      </div>
   </div>

   {{-- Pantalla/Alerta de Confirmación Exitosa --}}
   <div id="pantalla-exito" class="hidden text-center py-5 card border-0 shadow-sm rounded-lg mb-4">
      <div class="card-body">
         <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
         <h3 class="text-success font-weight-bold m-0">¡Ingreso registrado correctamente!</h3>
         <p class="text-muted font-weight-bold mt-2 h5" id="texto-espacio-confirmado">Espacio A00</p>
      </div>
   </div>

   {{-- Formulario Principal --}}
   <form action="{{ route('ingresos.store') }}" method="POST" id="form-ingreso">
      @csrf
      <input type="hidden" name="tipo_ingreso" id="tipo_ingreso" value="rfid">
      <input type="hidden" name="espacio_id" id="espacio_id_selected" value="">

      <div class="row" id="contenedor-principal">
         
         <!-- COLUMNA IZQUIERDA: CONTROLES Y DATOS DE ENTRADA -->
         <div class="col-lg-5 col-md-12 mb-4">
            
            <!-- Contenedor 1: Controles de Acceso (Modo, RFID / Visitante) -->
            <div class="card border-0 shadow-sm rounded-lg mb-4">
               <div class="card-header bg-white border-bottom py-3">
                  <h5 class="card-title font-weight-bold mb-0" style="color: #0b1b3d;">
                     <i class="fas fa-id-card text-primary mr-2"></i> Datos de Acceso
                  </h5>
               </div>
               <div class="card-body">
                  <!-- 1. Selector de Modo -->
                  @include('ingresos.partials.selector-modo')

                  <!-- 2. Panel RFID (Lector) -->
                  @include('ingresos.partials.panel-rfid')

                  <!-- 3. Ficha del Usuario (RFID Escaneado) -->
                  @include('ingresos.partials.panel-info-usuario')

                  <!-- 4. Formulario Visitante Manual -->
                  @include('ingresos.partials.panel-visitante')
               </div>
            </div>

            <!-- Contenedor 2: Confirmación y Resumen -->
            <div class="card border-0 shadow-sm rounded-lg">
               <div class="card-body">
                  <!-- 5. Banner Espacio + Botón Confirmar -->
                  @include('ingresos.partials.boton-confirmar')
               </div>
            </div>

         </div>

         <!-- COLUMNA DERECHA: GRILLA DE ESPACIOS -->
         <div class="col-lg-7 col-md-12">
            <div class="card border-0 shadow-sm rounded-lg">
               <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                  <h5 class="card-title font-weight-bold mb-0" style="color: #0b1b3d;">
                     <i class="fas fa-th text-primary mr-2"></i> Mapa de Espacios
                  </h5>
                  <small class="text-muted">Seleccione un sitio libre para asignar</small>
               </div>
               <div class="card-body">
                  @include('ingresos.partials.mapa-espacios')
               </div>
            </div>
         </div>

      </div>
   </form>

</div>
@endsection

@push('scripts')
   <script src="{{ asset('js/ingresos.js') }}"></script>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const inputRfid = document.getElementById('input_rfid');
         
         if (inputRfid) {
             function pollUltimaLectura() {
                 // Si no estamos en modo RFID o ya hay una tarjeta cargada en la vista, no hacemos polling
                 if (document.getElementById('tipo_ingreso').value !== 'rfid') return;
                 
                 // Si el panel de información del usuario ya está visible, ya se seleccionó una tarjeta
                 const panelInfo = document.getElementById('panel-info-usuario');
                 if (panelInfo && !panelInfo.classList.contains('hidden')) return;

                 fetch('{{ route("usuarios.ultimaTarjeta") }}') // Endpoint general que jala ultima_tarjeta_escaneada_
                     .then(response => response.json())
                     .then(data => {
                         if (data.codigo) {
                             inputRfid.value = data.codigo;
                             // Disparamos la búsqueda que ya existe en js/ingresos.js
                             consultarUsuarioRfid(data.codigo);
                         }
                     })
                     .catch(err => console.error('Error polling RFID:', err));
             }

             // Polling cada 1.5 segundos
             setInterval(pollUltimaLectura, 1500);
         }
      });
   </script>
@endpush