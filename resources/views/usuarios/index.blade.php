@extends('layouts.app')

@section('title', 'Gestión de Usuarios y RFID')
@section('header-title', 'Módulo de Usuarios')
@section('header-subtitle', 'Búsqueda por C.I., asignación de tarjetas y control de saldos')

@section('content')

    {{-- Buscador superior --}}
    @include('usuarios.partials.buscador')

    {{-- Notificación de Usuario No Encontrado --}}
    @if(session('error_no_encontrado'))

    @php($tarjeta = $usuario->tarjetaActual)

    <div class="card border-success mb-4">
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-user-slash mr-2"></i> {{ session('error_no_encontrado') }}
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