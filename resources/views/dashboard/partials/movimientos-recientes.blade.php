<!-- ÚLTIMOS MOVIMIENTOS DEL DÍA -->
<div class="card card-default">
   <div class="card-header d-flex align-items-center">
      <div class="card-title mb-0">
         <em class="fas fa-history text-success mr-2"></em>Últimos Movimientos del Día
      </div>
      <div class="ml-auto">
         <span class="badge badge-soft-info p-2" style="background-color: #e0f2fe; color: #0369a1; border-radius: 12px; font-weight: 600;">EN TIEMPO REAL</span>
      </div>
   </div>
   <div class="card-wrapper">
      <div class="table-responsive">
         <table class="table table-striped table-hover mb-0">
            <thead>
               <tr>
                  <th>HORA</th>
                  <th>NOMBRE</th>
                  <th>PLACA</th>
                  <th>TIPO</th>
                  <th>ACCIÓN</th>
                  <th>MONTO</th>
               </tr>
            </thead>
            <tbody>
               @forelse ($movimientos as $movimiento)
                  <tr>
                     <td>{{ $movimiento->hora_salida ? $movimiento->hora_salida->format('H:i') : $movimiento->hora_ingreso->format('H:i') }}</td>
                     <td>{{ $movimiento->tarjeta->usuario->nombre ?? '—' }}</td>
                     <td>{{ $movimiento->vehiculo->placa ?? '—' }}</td>
                     <td>{{ ucfirst($movimiento->vehiculo->tipo ?? '—') }}</td>
                     <td>
                        @if ($movimiento->estado === 'activo')
                           <span class="badge badge-success">Ingreso</span>
                        @else
                           <span class="badge badge-secondary">Salida</span>
                        @endif
                     </td>
                     <td>{{ $movimiento->pago ? 'Bs. ' . number_format($movimiento->pago->monto, 2) : '—' }}</td>
                  </tr>
               @empty
                  <tr>
                     <td colspan="6" class="text-center text-muted py-3">Todavía no hay movimientos registrados hoy.</td>
                  </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </div>
</div>