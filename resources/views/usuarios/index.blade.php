@extends('layouts.app')

@section('title', 'Gestión de Usuarios y RFID')
@section('header-title', 'Módulo de Usuarios')
@section('header-subtitle', 'Búsqueda por C.I., asignación de tarjetas y control de saldos')

@section('content')

    {{-- Buscador superior --}}
    @include('usuarios.partials.buscador')

    {{-- Notificación de Usuario No Encontrado o Mensajes de Éxito --}}
    @if(session('error_no_encontrado'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-user-slash mr-2"></i> {{ session('error_no_encontrado') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

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
        <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    {{-- Renderizado dinámico según búsqueda --}}
    @if(isset($usuario))
        @if($usuario->tarjetaActual)
            @include('usuarios.con-tarjeta')
        @else
            @include('usuarios.sin-tarjeta')
        @endif
    @else
        {{-- Muestra la tabla general si no hay una búsqueda activa --}}
        @include('usuarios.partials.tabla')
    @endif

@endsection

@push('scripts')
    @if ($errors->any() && (old('nombre') || old('ci') || old('vehiculos')))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                $('#modalAgregarUsuario').modal('show');
            }, 300);
        });
    </script>
    @endif
@endpush

@push('modals')
    {{-- Modal para agregar nuevo usuario --}}
    @include('usuarios.partials.modal_agregar')

    {{-- Modal para editar saldo directamente --}}
    @if(isset($usuario) && $usuario->tarjetaActual)
        @include('usuarios.partials.modal_editar_saldo', ['tarjeta' => $usuario->tarjetaActual])
    @endif

    {{-- Modal para editar datos de usuario --}}
    @if(isset($usuario))
        @include('usuarios.partials.modal_editar_usuario', ['usuario' => $usuario])
    @endif
@endpush