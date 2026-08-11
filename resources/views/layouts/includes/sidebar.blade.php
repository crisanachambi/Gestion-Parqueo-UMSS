<!-- ========================================================= -->
<!-- BARRA LATERAL (SIDEBAR)                                   -->
<!-- ========================================================= -->
<aside class="aside-container">
   <div class="aside-inner">
      <nav class="sidebar" data-sidebar-anyclick-close>
         <ul class="sidebar-nav">

            <!-- Info Usuario Autenticado -->
            <li class="has-user-block">
               <div id="user-block" class="collapse show">
                  <div class="item user-block">
                     <div class="user-block-content">
                        <div class="user-block-picture">
                           <img class="img-thumbnail rounded-circle" src="{{ asset('img/user/02.jpg') }}" alt="Avatar" width="60" height="60">
                        </div>
                        <div class="user-block-info">
                           <span class="user-block-name">
                              {{ Auth::user()?->nombre ?? 'Usuario' }} {{ Auth::user()?->apellido }}
                           </span>
                           <span class="user-block-role text-capitalize">
                              {{ Auth::user()?->rol == 'encargado' ? 'Encargado de Parqueo' : (Auth::user()?->rol ?? 'Cliente') }}
                           </span>
                        </div>
                     </div>
                  </div>
               </div>
            </li>

            <!-- 1. Dashboard -->
            <li class="{{ request()->is('dashboard*') ? 'active' : '' }}">
               <a href="{{ route('dashboard') }}" title="Dashboard">
                  <em class="fas fa-tachometer-alt"></em>
                  <span>Dashboard</span>
               </a>
            </li>

            <!-- 2. Usuarios y Vehículos (Submenú colapsable corregido) -->
            <li class="{{ request()->is('usuarios*') || request()->is('vehiculos*') ? 'active' : '' }}">
               <a href="#users-vehicles" data-toggle="collapse" title="Usuarios y Vehículos">
                  <em class="fas fa-users-cog"></em>
                  <span>Usuarios y Vehículos</span>
               </a>
               <ul class="sidebar-nav sidebar-subnav collapse {{ request()->is('usuarios*') || request()->is('vehiculos*') ? 'show' : '' }}" id="users-vehicles">
                  <li class="sidebar-subnav-header">Usuarios y Vehículos</li>
                  <li class="{{ request()->is('usuarios*') ? 'active' : '' }}">
                     <a href="{{ url('/usuarios') }}" title="Usuarios"><span>Usuarios</span></a>
                  </li>
                  <li class="{{ request()->is('vehiculos*') ? 'active' : '' }}">
                     <a href="{{ url('/vehiculos') }}" title="Vehículos"><span>Vehículos</span></a>
                  </li>
               </ul>
            </li>

            <!-- 3. Control de Acceso -->
            <li class="{{ request()->is('registros*') || request()->is('control-acceso*') ? 'active' : '' }}">
               <a href="{{ url('/registros') }}" title="Control de Acceso">
                  <em class="fas fa-door-open"></em>
                  <span>Control de Acceso</span>
               </a>
            </li>

            <!-- 4. Tarjetas Recargables -->
            <li class="{{ request()->is('tarjetas*') ? 'active' : '' }}">
               <a href="{{ url('/tarjetas') }}" title="Tarjetas Recargables">
                  <em class="fas fa-id-card"></em>
                  <span>Tarjetas Recargables</span>
               </a>
            </li>

            <!-- 5. Espacios y Capacidad -->
            <li class="{{ request()->is('espacios*') ? 'active' : '' }}">
               <a href="{{ url('/espacios') }}" title="Gestión de Espacios">
                  <em class="fas fa-parking"></em>
                  <span>Espacios</span>
               </a>
            </li>

            <!-- 6. Pagos y Recargas -->
            <li class="{{ request()->is('pagos*') || request()->is('recargas*') ? 'active' : '' }}">
               <a href="{{ url('/pagos') }}" title="Pagos y Recargas">
                  <em class="fas fa-money-bill-wave"></em>
                  <span>Pagos y Recargas</span>
               </a>
            </li>

            <!-- 7. Cerrar Sesión -->
            <li>
               <a href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
                  title="Cerrar sesión">
                  <em class="fas fa-sign-out-alt"></em>
                  <span>Cerrar sesión</span>
               </a>
               <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
               </form>
            </li>

         </ul>
      </nav>
   </div>
</aside>