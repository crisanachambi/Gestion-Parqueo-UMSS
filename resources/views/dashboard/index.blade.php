@extends('layouts.app')

@section('title', 'Dashboard - ParkUMSS')

@push('styles')
   <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="content-heading d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="m-0 font-weight-bold text-dark">Dashboard</h3>
    <small class="text-muted">Estado y gestión de {{ $parqueoActivo->nombre ?? 'su parqueo' }} en tiempo real</small>
  </div>
</div>

<!-- 1. SECCIÓN DE MÉTRICAS / RESUMEN -->
@include('dashboard.partials.kpi-cards')

<!-- 2. SECCIÓN PRINCIPAL: MAPA + VEHÍCULOS DENTRO -->
<div class="row">
  <!-- Mapa de Espacios (2/3 de pantalla) -->
  <div class="col-xl-8 col-lg-7 mb-4">
    @include('dashboard.partials.mapa-espacios')
  </div>

  <!-- Vehículos Actualmente Dentro (1/3 de pantalla) -->
  <div class="col-xl-4 col-lg-5 mb-4">
    @include('dashboard.partials.vehiculos-dentro')
  </div>
</div>

<!-- 3. SECCIÓN RFID -->
<div class="row">
  <div class="col-12 mb-4">
    @include('dashboard.partials.movimientos-rfid')
  </div>
</div>
@endsection