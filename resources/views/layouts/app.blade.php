<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'ParkUMSS - Sistema de Gestión de Parqueos')</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('img/logo-ummss.png') }}">

  <!-- 1. Bootstrap y FontAwesome (Librerías Base) -->
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">

  <!-- 2. Plantilla Base 47Admin -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  <!-- 3. Estilos Personalizados ParkUMSS (Color #0b1b3d, Mapa de Parqueo, KPIs) -->
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">

  <!-- Estilos adicionales por vista -->
  @stack('styles')
</head>

<body class="layout-fixed">
  <div class="wrapper">
    
    <!-- 1. Sidebar / Menú Lateral (Cargado primero para alineación absoluta desde arriba) -->
    @include('layouts.sidebar')

    <!-- 2. Header / Navbar Superior (Empieza a la derecha del Sidebar) -->
    @include('layouts.header')

    <!-- 3. Contenido Principal Dinámico -->
    <section class="section-container">
      <div class="content-wrapper">
        @yield('content')
      </div>
    </section>

    <!-- Footer / Pie de Página -->
    @include('layouts.footer')

  </div>

  <!-- Scripts Base de 47admin y Bootstrap -->
  <script src="{{ asset('js/app.js') }}"></script>

  <!-- Script para el Reloj en Tiempo Real -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-BO', { 
          hour: '2-digit', 
          minute: '2-digit', 
          second: '2-digit',
          hour12: true 
        });
        const clockElem = document.getElementById('live-clock');
        if (clockElem) {
          clockElem.textContent = timeString;
        }
      }
      setInterval(updateClock, 1000);
      updateClock();
    });
  </script>

  @stack('scripts')
</body>
</html>