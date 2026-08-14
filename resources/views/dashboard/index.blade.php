@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen del parqueo')

@section('content')
   {{-- Indicadores principales calculados en DashboardController --}}
   @include('dashboard.partials.resumen')
@endsection
