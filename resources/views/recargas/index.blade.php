<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>TITULAR</th>
            <th>RFID</th>
            <th>VEHÍCULO</th>
            <th>PLACA</th>
            <th>SALDO</th>
            <th>ESTADO</th>
            <th>ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tarjetas as $tarjeta)
        <tr>
            <td><strong>{{ $tarjeta->titular }}</strong></td>
            <td><code>{{ $tarjeta->rfid }}</code></td>
            <td><span class="badge badge-light"><i class="fas fa-car text-danger mr-1"></i> {{ $tarjeta->vehiculo }}</span></td>
            <td>{{ $tarjeta->placa }}</td>
            <td><strong class="text-success">{{ $tarjeta->saldo }} Bs</strong></td>
            <td><span class="badge badge-soft-success text-success">✓ Activa</span></td>
            <td>
                <button type="button" 
                        class="btn btn-sm btn-success btn-abrir-recarga px-3"
                        data-id="{{ $tarjeta->id }}"
                        data-nombre="{{ $tarjeta->titular }}"
                        data-rfid="{{ $tarjeta->rfid }}"
                        data-saldo="{{ $tarjeta->saldo }}">
                    Recargar
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('recargas.partials.modal-recargar')

@push
{{-- Al final de resources/views/recargas/index.blade.php --}}
@push('scripts')
    <script src="{{ asset('js/recargas.js') }}"></script>
@endpush