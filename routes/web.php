<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ControllerLogin;
// Agrega este import arriba, junto a los demás:
use App\Http\Controllers\DashboardController;

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
// Raíz: si hay sesión activa va al dashboard, si no, al login.
Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
});
 
// Rutas públicas de autenticación (solo para visitantes sin sesión)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [ControllerLogin::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ControllerLogin::class, 'login'])->name('login.attempt');
});
 
// Logout: requiere sesión activa, no "guest"
Route::post('/logout', [ControllerLogin::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
 
// Rutas protegidas (solo accesibles si iniciaste sesión)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});
 
