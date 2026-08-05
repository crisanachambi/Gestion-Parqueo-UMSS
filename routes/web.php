<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ControllerLogin;

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

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas de autenticación
Route::get('/login', [ControllerLogin::class, 'showLoginForm'])->name('login');
Route::post('/login', [ControllerLogin::class, 'login']);
Route::post('/logout', [ControllerLogin::class, 'logout'])->name('logout');

// Rutas protegidas (solo accesibles si iniciaste sesión)
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});