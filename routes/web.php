<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Parkumss\Auth\ControllerLogin;
use App\Http\Controllers\Parkumss\DashboardController;
use App\Http\Controllers\Parkumss\IngresoController;
use App\Http\Controllers\Parkumss\TarjetaController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------
| Rutas de ParkUMSS
|--------------------------------------------------------------
| Todo el panel exige encargado autenticado. El Global Scope
| se apoya en auth(), asi que sin sesion nada funciona bien.
*/

Route::get('/', fn () => redirect()->route('login'));
 
// ---------------- Publicas ----------------
Route::middleware('guest')->group(function () {
    Route::get('login',  [ControllerLogin::class, 'showLoginForm'])->name('login');
    Route::post('login', [ControllerLogin::class, 'login'])->name('login.attempt');
});
 
Route::post('logout', [ControllerLogin::class, 'logout'])
     ->middleware('auth')->name('logout');
 
// ---------------- Panel del encargado ----------------
Route::middleware('auth')->group(function () {
 
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
        // ---- Ingresos ----
    Route::prefix('ingresos')->name('ingresos.')->group(function () {
        Route::get('/', [IngresoController::class, 'index'])->name('index');
        Route::post('/', [IngresoController::class, 'store'])->name('store');
        Route::get('rfid/buscar', [IngresoController::class, 'buscarRfid'])->name('rfid');
    });

    // ---- Salidas ----
    Route::prefix('salidas')->name('salidas.')->group(function () {
        Route::get('/', [IngresoController::class, 'salidas'])->name('index');
        Route::post('{registro}', [IngresoController::class, 'salida'])->name('registrar');
    });
 
    // ---- Usuarios y tarjetas ----
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [TarjetaController::class, 'index'])->name('index');
        Route::post('buscar-ci', [TarjetaController::class, 'buscarPorCi'])->name('buscarCi');
        Route::post('/', [TarjetaController::class, 'store'])->name('store');
        Route::post('{tarjeta}/bloquear', [TarjetaController::class, 'bloquear'])->name('bloquear');
    });
 
    // ---- Recargas ----
    Route::prefix('recargas')->name('recargas.')->group(function () {
        Route::get('/', [TarjetaController::class, 'recargas'])->name('index');
        Route::post('{tarjeta}', [TarjetaController::class, 'recargar'])->name('store');
    });
 
    // ---- Pendientes de implementar ----
    // Declaradas para que el sidebar no rompa las paginas.
    Route::get('reportes', fn () => view('reportes.index'))->name('reportes.index');
    Route::get('ajustes',  fn () => view('ajustes.index'))->name('ajustes.index');
});
 
