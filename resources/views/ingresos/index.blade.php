@extends('layouts.app')

@section('title', 'Registrar Ingreso - ParkUMSS')

@push('styles')
   <link rel="stylesheet" href="{{ asset('css/ingresos.css') }}">
@endpush

@section('content')
<div class="content-wrapper">

   <!-- Encabezado Estático -->
   <div class="content-heading d-flex justify-content-between align-items-center mb-4">
      <div>
         <h3 class="m-0 font-weight-bold" style="color: #0b1b3d;">Registrar ingreso</h3>
         <small class="text-muted">Gestión de accesos y asignación de espacios</small>
      </div>
      <div>
         <span class="label label-success p-2 font-weight-bold">
            <em class="fa fa-parking mr-1"></em> {{ $parqueo->nombre }}
         </span>
      </div>
   </div>

   {{-- Pantalla/Alerta de Confirmación Exitosa --}}
   <div id="pantalla-exito" class="hidden text-center pv-xl">
      <em class="fa fa-check-circle fa-4x text-success mb-3 block"></em>
      <h3 class="text-success font-weight-bold m0">Ingreso registrado</h3>
      <p class="text-muted font-weight-bold mt-2" id="texto-espacio-confirmado">Espacio A00</p>
   </div>

   {{-- Formulario Principal --}}
   <form action="{{ route('ingresos.store') }}" method="POST" id="form-ingreso">
      @csrf
      <input type="hidden" name="tipo_ingreso" id="tipo_ingreso" value="rfid">
      <input type="hidden" name="espacio_id" id="espacio_id_selected" value="">

      <div class="row" id="contenedor-principal">
         
         <!-- COLUMNA IZQUIERDA: CONTROLES DE ENTRADA -->
         <div class="col-md-5">
            <!-- 1. Estático: Selector de Modo -->
            @include('ingresos.partials.selector-modo')

            <!-- 2. Dinámico: Panel RFID (Lector) -->
            @include('ingresos.partials.panel-rfid')

            <!-- 3. Dinámico: Ficha del Usuario (RFID Escaneado) -->
            @include('ingresos.partials.panel-info-usuario')

            <!-- 4. Dinámico: Formulario Visitante Manual -->
            @include('ingresos.partials.panel-visitante')

            <!-- 5. Estático/Dinámico: Banner Espacio + Botón Confirmar -->
            @include('ingresos.partials.boton-confirmar')
         </div>

         <!-- COLUMNA DERECHA: GRILLA DE ESPACIOS -->
         <div class="col-md-7">
            @include('ingresos.partials.mapa-espacios')
         </div>

      </div>
   </form>

</div>
@endsection

@push('scripts')
   <script src="{{ asset('js/ingresos.js') }}"></script>
@endpush