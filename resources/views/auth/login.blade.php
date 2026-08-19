<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="Sistema de Parqueos UMSS - Bootstrap Admin App">
   <meta name="keywords" content="app, responsive, jquery, bootstrap, admin, ummss, parqueos">
   <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
   <title>UMSS Parqueos - Iniciar Sesión</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/brands.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/regular.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/solid.css') }}">
   <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/fontawesome.css') }}">
   <!-- ANIMATE.CSS-->
   <link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}">
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}" id="bscss">
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="{{ asset('css/app.css') }}" id="maincss">
   <!-- =============== UMSS CUSTOM STYLES (no se toca app.css) ===============-->
   <!-- Esta hoja SOLO sobreescribe estilos visuales. No agrega ni quita funcionalidad. -->
   <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}" id="ummsscss">
</head>

<body class="ummss-login-page">
   <div class="wrapper">
      <div class="full-page-background bg-darker"></div>

      <!-- Contenedor general: usa Bootstrap grid, no lo reemplaza -->
      <div class="container-fluid h-100 d-flex flex-column p-0">

         <!-- FILA PRINCIPAL: 2 columnas (branding + formulario) -->
         <div class="row no-gutters flex-grow-1 align-items-stretch">

            <!-- ============ COLUMNA IZQUIERDA: BRANDING UMSS ============ -->
            <!-- d-none d-lg-flex -> exactamente el patrón responsive que pidió el cliente:
                 oculta en móvil/tablet, visible desde el breakpoint lg en adelante -->
            <div class="col-lg-6 d-none d-lg-flex flex-column align-items-center justify-content-center ummss-brand-col">

               <div class="ummss-brand-content text-center animated fadeIn">
                  <!-- LOGO GRANDE -->
                  <img src="{{ asset('img/logo-ummss.png') }}" alt="UMSS Parqueos" class="ummss-logo-large mb-4">

                  <!-- Wordmark minimalista en lugar del texto largo original -->
                  <div class="ummss-wordmark mb-5">U M S S</div>

                  <!-- Tres iconos: Pagos / Monitoreo / Cobros -->
                  <div class="d-flex justify-content-center ummss-feature-icons">
                     <div class="ummss-feature-item">
                        <span class="ummss-icon-circle ummss-icon-blue">
                           <em class="fas fa-mobile-alt"></em>
                        </span>
                        <span class="ummss-feature-label">Pagos</span>
                     </div>
                     <div class="ummss-feature-item">
                        <span class="ummss-icon-circle ummss-icon-white">
                           <em class="fas fa-chart-line"></em>
                        </span>
                        <span class="ummss-feature-label">Monitoreo</span>
                     </div>
                     <div class="ummss-feature-item">
                        <span class="ummss-icon-circle ummss-icon-red">
                           <em class="fas fa-receipt"></em>
                        </span>
                        <span class="ummss-feature-label">Cobros</span>
                     </div>
                  </div>
               </div>

               <!-- Footer institucional discreto, dentro de la misma columna -->
               <div class="ummss-brand-footer text-center">
                  <div class="ummss-brand-footer-title">Universidad Mayor de San Simón</div>
                  <div class="ummss-brand-footer-tag">
                     <span class="text-blue">DIGITAL</span> &bull; SEGURO &bull; <span class="text-red">EFICIENTE</span>
                  </div>
                  <div class="ummss-brand-footer-copy">&copy; {{ date('Y') }} UMSS Parqueos - Todos los derechos reservados</div>
               </div>
            </div>

            <!-- ============ COLUMNA DERECHA: FORMULARIO 47ADMIN (intacto) ============ -->
            <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center ummss-form-col">

               <!-- START card: misma clase base card-flat del template -->
               <div class="card card-flat ummss-card" style="min-width: 300px">
                  <div class="card-header text-center bg-transparent border-0">
                     <!-- Logo pequeño solo visible cuando el branding grande está oculto (móvil/tablet) -->
                     <a href="#" class="d-lg-none d-inline-block mb-2">
                        <img class="ummss-logo-small" src="{{ asset('img/logo-ummss.png') }}" alt="UMSS">
                     </a>
                  </div>
                  <div class="card-body">

                     <h1 class="ummss-title text-center mb-1">Bienvenido</h1>
                     <p class="text-center ummss-subtitle mb-4">Inicia sesión para continuar</p>

                     <!-- MISMO FORMULARIO ORIGINAL: mismos IDs, mismo required, mismo novalidate -->
                     <form id="loginForm" method="POST" action="{{ route('login.attempt') }}" novalidate>
                        @csrf

                        {{-- Mensajes del ControllerLogin: credenciales
                             incorrectas, cuenta sin parqueo, etc. --}}
                        @if ($errors->any())
                           <div class="alert alert-danger" role="alert">
                              <em class="fas fa-exclamation-circle mr-1"></em>
                              {{ $errors->first() }}
                           </div>
                        @endif

                        <div class="form-group">
                           <label for="exampleInputEmail1" class="ummss-label">USUARIO</label>
                           <div class="input-group with-focus">
                              <div class="input-group-prepend">
                                 <span class="input-group-text bg-transparent border-right-0"><em class="fas fa-user"></em></span>
                              </div>
                              <input class="form-control border-left-0" id="exampleInputEmail1" name="email" type="email" value="{{ old('email') }}" placeholder="Ingresa tu usuario" autocomplete="email" required>
                           </div>
                        </div>
                        <div class="form-group">
                           <label for="exampleInputPassword1" class="ummss-label">CONTRASEÑA</label>
                           <div class="input-group with-focus">
                              <div class="input-group-prepend">
                                 <span class="input-group-text bg-transparent border-right-0"><em class="fas fa-lock"></em></span>
                              </div>
                              <input class="form-control border-left-0 border-right-0" id="exampleInputPassword1" name="password" type="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                              <div class="input-group-append">
                                 <span class="input-group-text bg-transparent border-left-0 ummss-pass-toggle" id="ummssTogglePassword" role="button">
                                    <em class="fas fa-eye"></em>
                                 </span>
                              </div>
                           </div>
                        </div>
                        <div class="clearfix">
                           <div class="custom-control custom-checkbox float-left mt-0">
                              <input class="custom-control-input" id="rememberme" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                              <label class="custom-control-label ummss-remember-label" for="rememberme">Recordarme</label>
                           </div>
                           
                        </div>
                        <button class="btn btn-block ummss-btn-primary mt-4" type="submit">
                           <em class="fas fa-sign-in-alt mr-2"></em>INGRESAR
                        </button>
                     </form>

                     <div class="ummss-divider my-4"><span>o</span></div>

        
                     <p class="pt-3 text-center ummss-register-text">
                        <a class="ummss-link" href="#">¿Olvidaste tu contraseña?</a>
                     </p>
                  </div>
               </div>
               <!-- END card-->
            </div>

         </div>
      </div>
   </div>

   <!-- =============== VENDOR SCRIPTS (sin tocar) ===============-->
   <!-- STORAGE API-->
   <script src="{{ asset('vendor/js-storage/js.storage.js') }}"></script>
   <!-- i18next-->
   <script src="{{ asset('vendor/i18next/i18next.js') }}"></script>
   <script src="{{ asset('vendor/i18next-xhr-backend/i18nextXHRBackend.js') }}"></script>
   <!-- JQUERY-->
   <script src="{{ asset('vendor/jquery/dist/jquery.js') }}"></script>
   <!-- BOOTSTRAP-->
   <script src="{{ asset('vendor/popper.js/dist/umd/popper.js') }}"></script>
   <script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.js') }}"></script>
   <!-- PARSLEY-->
   <script src="{{ asset('vendor/parsleyjs/dist/parsley.js') }}"></script>
   <!-- =============== APP SCRIPTS (sin tocar) ===============-->
   <script src="{{ asset('js/app.js') }}"></script>

   <!-- =============== SCRIPT ADICIONAL UMSS: solo toggle de contraseña (no afecta app.js) ===============-->
   <script>
      document.addEventListener('DOMContentLoaded', function () {
         var toggle = document.getElementById('ummssTogglePassword');
         var pass = document.getElementById('exampleInputPassword1');
         if (toggle && pass) {
            toggle.addEventListener('click', function () {
               var isPassword = pass.getAttribute('type') === 'password';
               pass.setAttribute('type', isPassword ? 'text' : 'password');
               toggle.querySelector('em').classList.toggle('fa-eye');
               toggle.querySelector('em').classList.toggle('fa-eye-slash');
            });
         }
      });
   </script>
</body>

</html>