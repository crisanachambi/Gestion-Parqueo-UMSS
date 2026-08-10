<!-- ========================================================= -->
<!-- BARRA SUPERIOR (NAVBAR)                                   -->
<!-- ========================================================= -->
<header class="topnavbar-wrapper">
   <nav class="navbar topnavbar">
      <div class="navbar-header">
         <a class="navbar-brand" href="{{ url('/dashboard') }}">
            <div class="brand-logo"><img class="img-fluid" src="{{ asset('img/logo-ummss') }}" alt="App Logo"></div>
            <div class="brand-logo-collapsed"><img class="img-fluid" src="{{ asset('img/logo-single.png') }}" alt="App Logo"></div>
         </a>
      </div>

      <ul class="navbar-nav mr-auto flex-row">
         <li class="nav-item">
            <a class="nav-link d-none d-md-block" href="#" data-trigger-resize="" data-toggle-state="aside-collapsed">
               <em class="fas fa-align-left"></em>
            </a>
            <a class="nav-link sidebar-toggle d-md-none" href="#" data-toggle-state="aside-toggled" data-no-persist="true">
               <em class="fas fa-align-left"></em>
            </a>
         </li>
      </ul>

      <ul class="navbar-nav flex-row">
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-toggle="dropdown">
               <em class="fas fa-user"></em>
            </a>
            <div class="dropdown-menu dropdown-menu-right animated bounceIn">
               <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                  <em class="fas fa-sign-out-alt mr-2 text-muted"></em>Cerrar sesión
               </a>
               <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
               </form>
            </div>
         </li>
      </ul>
   </nav>
</header>