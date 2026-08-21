<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<aside class="aside-container aside-custom">
   <div class="aside-inner aside-inner-custom d-flex flex-column justify-content-between h-100 pb-2">
      
      <!-- SECCIÓN SUPERIOR -->
      <div>
         
         <!-- 1. LOGO PARKUMSS -->
         <div class="sidebar-brand-card">
            <div>
               <img src="{{ asset('img/logo-ummss.png') }}" 
                    onerror="this.onerror=null; this.src='{{ asset('img/logo-ummss.jpg') }}';" 
                    alt="ParkUMSS Logo" 
                    class="brand-img">
            </div>
            <span class="brand-sub">Sistema de parqueos</span>
         </div>

         <!-- 2. PARQUEO ACTIVO -->
         <div class="sidebar-parqueo-card">
            <span class="label-muted">PARQUEO ACTIVO</span>
            <span class="parqueo-name">
               {{ session('parqueo_activo_nombre', auth()->user()->parqueo->nombre ?? 'Parqueo Facultad de Economía') }}
            </span>
         </div>

         <!-- 3. MENÚ CON ICONOS -->
         <ul class="sidebar-menu-list">
            <li class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
               <a href="{{ route('dashboard') }}">
                  <i class="fas fa-home"></i>
                  <span>Dashboard</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('ingresos*') ? 'active' : '' }}">
               <a href="{{ route('ingresos.index') }}">
                  <i class="far fa-id-card"></i>
                  <span>Ingreso</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('salidas*') ? 'active' : '' }}">
               <a href="{{ route('salidas.index') }}">
                  <i class="fas fa-clipboard-check"></i>
                  <span>Salida</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('recargas*') ? 'active' : '' }}">
               <a href="{{ route('recargas.index') }}">
                  <i class="fas fa-chart-line"></i>
                  <span>Recargas</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('usuarios*') ? 'active' : '' }}">
               <a href="{{ route('usuarios.index') }}">
                  <i class="fas fa-user-friends"></i>
                  <span>Usuarios</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('reportes*') ? 'active' : '' }}">
               <a href="{{ route('reportes.index') }}">
                  <i class="far fa-file-alt"></i>
                  <span>Reportes</span>
               </a>
            </li>
            <li class="{{ request()->routeIs('ajustes*') ? 'active' : '' }}">
               <a href="{{ route('ajustes.index') }}">
                  <i class="fas fa-cog"></i>
                  <span>Ajustes</span>
               </a>
            </li>
         </ul>

      </div>

      <!-- SECCIÓN INFERIOR (USUARIO Y CIRCUITO) -->
      <div class="sidebar-footer-wrapper">
         
         <!-- SLOGAN -->
         <div class="slogan-footer">
            <span class="slogan-digital">DIGITAL</span> · 
            <span class="slogan-seguro">SEGURO</span> · 
            <span class="slogan-eficiente">EFICIENTE</span>
         </div>

         <!-- DECORATIVO DE CIRCUITO -->
         <div class="circuit-bg"></div>

         <!-- USUARIO AUTENTICADO -->
         <div class="user-profile-box">
            <div class="d-flex align-items-center" style="overflow: hidden;">
               <div class="user-avatar-circle">
                  {{ strtoupper(substr(Auth::user()->nombre ?? Auth::user()->name ?? 'A', 0, 1)) }}
               </div>
               <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  <strong class="text-white d-block" style="font-size: 12px; line-height: 1.1; overflow: hidden; text-overflow: ellipsis;">
                     {{ Auth::user()->nombre_completo ?? Auth::user()->name ?? 'Ana Quispe' }}
                  </strong>
                  <span class="d-block" style="color: var(--ummss-gray); font-size: 10px;">
                     {{ ucfirst(Auth::user()->rol ?? 'Operador') }}
                  </span>
               </div>
            </div>

            <!-- BOTÓN CERRAR SESIÓN -->
            <form method="POST" action="{{ route('logout') }}" class="m-0">
               @csrf
               <button type="submit" class="btn-logout-icon" title="Cerrar Sesión">
                  <i class="fas fa-sign-out-alt"></i>
               </button>
            </form>
         </div>

      </div>

   </div>
</aside>