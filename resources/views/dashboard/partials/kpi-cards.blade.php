<div class="row mb-4">

  <!-- Ocupación Actual -->
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100 p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon-box bg-light-success text-success mr-3">
          <i class="fa fa-th-large fa-lg"></i>
        </div>
        <div>
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">OCUPACIÓN ACTUAL</small>
          <div class="h3 font-weight-bold m-0 text-dark">
            {{ $ocupadosCount }}/{{ $capacidadTotal }}
          </div>
          <small class="text-muted">{{ $porcentajeOcupacion }}% · {{ $disponiblesCount }} libres</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Ingresos Activos -->
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100 p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon-box bg-light-primary text-primary mr-3">
          <i class="fa fa-users fa-lg"></i>
        </div>
        <div>
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">INGRESOS ACTIVOS</small>
          <div class="h3 font-weight-bold m-0 text-dark">{{ $ingresosActivosCount }}</div>
          <small class="text-muted">
            {{ $ingresosTarjeta }} con tarjeta · {{ $ingresosVisitantes }} visitantes
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Recaudación del Día -->
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100 p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon-box bg-light-warning text-warning mr-3">
          <i class="fa fa-clock-o fa-lg"></i>
        </div>
        <div>
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">RECAUDACIÓN DEL DÍA</small>
          <div class="h3 font-weight-bold m-0 text-dark">{{ number_format($recaudacionTotal, 2) }} <small class="h5">Bs</small></div>
          <small class="text-muted">
            Tarjeta: {{ number_format($recaudacionTarjeta, 2) }} · Efectivo: {{ number_format($recaudacionEfectivo, 2) }}
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Recargas del Día -->
  <div class="col-xl-3 col-md-6 mb-3">
    <div class="card border-0 shadow-sm h-100 p-3">
      <div class="d-flex align-items-center">
        <div class="kpi-icon-box bg-light-info text-info mr-3">
          <i class="fa fa-refresh fa-lg"></i>
        </div>
        <div>
          <small class="text-uppercase text-muted font-weight-bold" style="font-size: 10px;">RECARGAS DEL DÍA</small>
          <div class="h3 font-weight-bold m-0 text-dark">{{ $recargasCount }}</div>
          <small class="text-muted">Total recargado: {{ number_format($montoRecargadoTotal, 2) }} Bs</small>
        </div>
      </div>
    </div>
  </div>

</div>