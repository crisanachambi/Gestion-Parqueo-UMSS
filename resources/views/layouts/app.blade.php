<!DOCTYPE html>
<html lang="es">
 
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="Sistema de Gestión de Parqueo">
   <title>@yield('title', 'Sistema de Parqueo')</title>
 
   <!-- =============== VENDOR STYLES ===============-->
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/brands.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/regular.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/solid.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/fontawesome.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}">
 
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}" id="bscss">
 
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="{{ asset('css/app.css') }}" id="maincss">
   @stack('styles')
</head>
 
<body>
   <div class="wrapper">
 
      @include('layouts.includes.header')
 
      @include('layouts.includes.sidebar')
 
      <!-- ========================================================= -->
      <!-- CONTENIDO PRINCIPAL                                       -->
      <!-- ========================================================= -->
      <section class="section-container">
         <div class="content-wrapper">
            <div class="content-header">
               <div class="content-title">
                  @yield('page-title', 'Dashboard')
                  <br><small>@yield('page-subtitle', 'Bienvenido al sistema')</small>
               </div>
               @yield('header-actions')
            </div>
 
            <!-- Inyección de la vista del hijo -->
            @yield('content')
         </div>
      </section>
 
      @include('layouts.includes.footer')
 
   </div>
 
   <!-- =============== SCRIPTS ===============-->
   <script src="{{ asset('vendor/jquery/dist/jquery.js') }}"></script>
   <script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.bundle.js') }}"></script>
   <script src="{{ asset('js/app.js') }}"></script>
   @stack('scripts')
</body>
 
</html>