@extends('layouts.app')

@section('title', 'Dashboard - ParkUMSS')

@push('styles')
   <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="content-heading d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="m-0 font-weight-bold text-dark">Dashboard</h3>
    <small class="text-muted">Estado y gestión de {{ $parqueoActivo->nombre ?? 'su parqueo' }} en tiempo real</small>
  </div>
  <div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalRecargaRapida">
      <i class="fas fa-wallet mr-1"></i> Recargar
    </button>
  </div>
</div>

@if(session('exito'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('exito') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div id="dashboard-content">

<!-- 1. SECCIÓN DE MÉTRICAS / RESUMEN -->
@include('dashboard.partials.kpi-cards')

<!-- 2. SECCIÓN PRINCIPAL: MAPA + VEHÍCULOS DENTRO -->
<div class="row">
  <!-- Mapa de Espacios (2/3 de pantalla) -->
  <div class="col-xl-8 col-lg-7 mb-4">
    @include('dashboard.partials.mapa-espacios')
  </div>

  <!-- Vehículos Actualmente Dentro (1/3 de pantalla) -->
  <div class="col-xl-4 col-lg-5 mb-4">
    @include('dashboard.partials.vehiculos-dentro')
  </div>
</div>

<!-- 3. SECCIÓN RFID -->
<div class="row">
  <div class="col-12 mb-4">
    @include('dashboard.partials.movimientos-rfid')
  </div>
</div>
</div> <!-- End #dashboard-content -->

@push('modals')
<!-- Modal Recarga Rápida -->
<div class="modal fade" id="modalRecargaRapida" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-wallet mr-2"></i> Recarga Rápida RFID</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-5" id="recarga-step-1">
                <p class="text-muted mb-4 font-weight-bold">Acerque la tarjeta al lector para recargar</p>
                <div class="mb-4">
                    <i class="fas fa-wifi fa-5x text-success opacity-50" style="animation: pulse 2s infinite;"></i>
                </div>
                <div class="input-group mx-auto" style="max-width: 300px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0 text-muted"><i class="fas fa-microchip"></i></span>
                    </div>
                    <input type="text" id="rfid_rapido" class="form-control border-left-0 bg-light text-center" placeholder="Esperando tarjeta..." readonly>
                </div>
                <div id="error-rfid-rapido" class="text-danger mt-3 font-weight-bold" style="display:none;"></div>
            </div>

            <!-- Paso 2: Mostrar datos y confirmar -->
            <form id="formRecargaRapida" action="" method="POST" style="display:none;" onsubmit="return confirm('¿Estás seguro de recargar ' + this.monto.value + ' Bs. a este saldo?');">
                @csrf
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="mb-2"><i class="fas fa-user-circle fa-4x text-secondary"></i></div>
                        <h4 id="rr-nombre" class="font-weight-bold text-dark mb-1"></h4>
                        <span class="badge badge-light border text-muted px-3 py-1" id="rr-ci"></span>
                    </div>
                    <div class="alert alert-success text-center mb-4 border-0 shadow-sm" style="background-color: #f0fdf4;">
                        <span class="d-block text-success font-weight-bold mb-1" style="font-size:0.9rem; letter-spacing: 1px;">SALDO ACTUAL</span>
                        <h2 class="mb-0 font-weight-bold text-success" id="rr-saldo"></h2>
                    </div>
                    <input type="hidden" name="tarjeta_id" id="rr-tarjeta-id">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">Monto a Recargar (Bs.)</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white text-success font-weight-bold border-right-0">Bs.</span>
                            </div>
                            <input type="number" name="monto" class="form-control text-center font-weight-bold border-left-0" style="font-size:1.5rem;" min="1" max="1000" step="0.5" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-light border btn-cancelar-rr font-weight-bold px-4">Cancelar</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm"><i class="fas fa-check-circle mr-1"></i> Confirmar Recarga</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<style>
@keyframes pulse { 0% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 1; } 100% { transform: scale(1); opacity: 0.5; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rfidBuffer = '';
    let lastKeyTime = Date.now();
    
    // Función común para procesar el RFID (usada por USB y por ESP32)
    function procesarRfid(codigo_leido) {
        const rfidInput = document.getElementById('rfid_rapido');
        if (rfidInput) rfidInput.value = codigo_leido;
        
        fetch(`{{ route('recargas.buscarRfid') }}?codigo=${codigo_leido}`)
            .then(res => {
                if (!res.ok) throw new Error('Tarjeta no encontrada');
                return res.json();
            })
            .then(data => {
                document.getElementById('recarga-step-1').style.display = 'none';
                document.getElementById('formRecargaRapida').style.display = 'block';
                
                // Asegurar que la URL sea /recargas/{id}
                document.getElementById('formRecargaRapida').action = `{{ url('recargas') }}/${data.tarjeta_id}`;
                
                document.getElementById('rr-tarjeta-id').value = data.tarjeta_id;
                document.getElementById('rr-nombre').textContent = data.nombre;
                document.getElementById('rr-ci').textContent = `C.I.: ${data.ci}`;
                document.getElementById('rr-saldo').textContent = `${data.saldo.toFixed(2)} Bs`;
                
                document.getElementById('error-rfid-rapido').style.display = 'none';
                
                setTimeout(() => document.querySelector('#formRecargaRapida input[name="monto"]').focus(), 200);
            })
            .catch(err => {
                const errDiv = document.getElementById('error-rfid-rapido');
                errDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Tarjeta no registrada o inactiva.';
                errDiv.style.display = 'block';
            });
    }

    // --- 1. Polling para el escáner ESP32 ---
    let pollingInterval = null;

    function pollUltimaTarjeta() {
        // No hacer polling si ya estamos en el paso 2
        if (document.getElementById('formRecargaRapida').style.display !== 'none') return;

        fetch('{{ route("usuarios.ultimaTarjeta") }}')
            .then(response => response.json())
            .then(data => {
                if (data.codigo) {
                    procesarRfid(data.codigo);
                }
            })
            .catch(err => console.error('Error polling RFID:', err));
    }

    $('#modalRecargaRapida').on('shown.bs.modal', function () {
        if (!pollingInterval) {
            pollingInterval = setInterval(pollUltimaTarjeta, 1500);
        }
    });

    $('#modalRecargaRapida').on('hidden.bs.modal', function () {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    });

    // --- 2. Listener para escáneres USB (emuladores de teclado) ---
    window.addEventListener('keydown', function(e) {
        const modal = document.getElementById('modalRecargaRapida');
        if (!modal || !modal.classList.contains('show')) return;

        if (document.getElementById('formRecargaRapida').style.display !== 'none') return;

        const currentTime = Date.now();
        if (currentTime - lastKeyTime > 50) {
            rfidBuffer = '';
        }
        lastKeyTime = currentTime;

        if (e.key.length === 1) rfidBuffer += e.key;

        if (e.key === 'Enter' && rfidBuffer.length >= 5) {
            e.preventDefault();
            procesarRfid(rfidBuffer);
            rfidBuffer = '';
        }
    });

    $('.btn-cancelar-rr').on('click', resetModalRr);
    $('#modalRecargaRapida').on('hidden.bs.modal', resetModalRr);
    
    function resetModalRr() {
        document.getElementById('recarga-step-1').style.display = 'block';
        document.getElementById('formRecargaRapida').style.display = 'none';
        document.getElementById('rfid_rapido').value = '';
        document.getElementById('error-rfid-rapido').style.display = 'none';
        document.querySelector('#formRecargaRapida input[name="monto"]').value = '';
    }

    // Auto-refresh Dashboard silently every 5 seconds
    setInterval(() => {
        if (!$('.modal').hasClass('show')) {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newDashboard = doc.getElementById('dashboard-content');
                    if (newDashboard) {
                        document.getElementById('dashboard-content').innerHTML = newDashboard.innerHTML;
                    }
                })
                .catch(err => console.log('Error refreshing dashboard:', err));
        }
    }, 5000);
});
</script>
@endpush
@endsection