<!-- OCUPACIÓN DEL PARQUEO -->
<div class="card card-default">
   <div class="card-header">
      <div class="card-title mb-0">
         <span class="badge bg-success text-white mr-1" style="padding: 3px 6px;">P</span> Ocupación del Parqueo
      </div>
   </div>
   <div class="card-body text-center">
      <h1 class="display-4 font-weight-bold text-success my-2">{{ $ocupacionPorcentaje }}%</h1>
      <p class="text-muted">ocupado</p>
      <div class="progress progress-xs mb-3">
         <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ocupacionPorcentaje }}%;"></div>
      </div>
      <div class="d-flex justify-content-between text-sm fw-bold">
         <span class="text-success"><em class="fas fa-circle mr-1"></em>{{ $espaciosLibres }} libres</span>
         <span class="text-danger"><em class="fas fa-circle mr-1"></em>{{ $espaciosOcupados }} ocupados</span>
      </div>
   </div>
</div>