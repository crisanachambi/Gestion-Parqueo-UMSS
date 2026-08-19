<header class="topnavbar-wrapper">
   <nav class="navbar topnavbar-custom d-flex align-items-center justify-content-between px-3">
      
      <!-- LADO IZQUIERDO: Botón Toggle Responsivo para Móviles -->
      <div class="d-flex align-items-center">
         <button type="button" 
                  class="btn btn-link text-white d-lg-none me-2 p-0 border-0" 
                  id="sidebar-toggle-btn"
                  onclick="document.body.classList.toggle('aside-toggled')"
                  title="Abrir/Cerrar Menú">
               <i class="fas fa-bars fs-4"></i>
         </button>
      </div>

      <!-- LADO DERECHO: Reloj y Parqueo Activo -->
      <div class="d-flex align-items-center flex-wrap justify-content-end">
         <div id="live-clock" class="clock-digital me-2">
            00:00:00 AM
         </div>

         <span class="badge-topbar-red">
            <i class="fas fa-circle text-danger me-1 style-dot"></i> 
            <span class="d-none d-sm-inline">{{ session('parqueo_activo_nombre', $parqueoActivo->nombre ?? 'PARQUEO ECONOMÍA') }}</span>
            <span class="d-inline d-sm-none">ECONOMÍA</span>
         </span>
      </div>

   </nav>
</header>