<?php

namespace App\Http\Controllers\Parkumss\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControllerLogin extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
 
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'El correo electronico es obligatorio.',
            'email.email'       => 'Ingresa un correo electronico valido.',
            'password.required' => 'La contrasena es obligatoria.',
        ]);
 
        $remember = $request->has('remember');
 
        if (! Auth::attempt($credentials, $remember)) {
            return $this->rechazar('Las credenciales proporcionadas no coinciden con nuestros registros.');
        }
 
        $usuario = Auth::user();
 
        // El panel es solo para encargados. Los usuarios de la
        // app movil entran por la API, no por aqui.
        if (! $usuario->esEncargado()) {
            return $this->rechazar('Esta cuenta no tiene acceso al panel.', true);
        }
 
        if (! $usuario->activo) {
            return $this->rechazar('Esta cuenta esta desactivada.', true);
        }
 
        // Un encargado sin parqueo rompe el Global Scope:
        // parqueoActual() devolveria null y no filtraria nada.
        if (! $usuario->parqueo_asignado_id) {
            return $this->rechazar('Su cuenta no tiene un parqueo asignado. Contacte al administrador.', true);
        }
 
        // Evita el secuestro de sesion tras autenticar.
        $request->session()->regenerate();
 
        // Copia para el header y el sidebar. La fuente de verdad
        // sigue siendo el usuario, no la sesion.
        session([
            'parqueo_id'     => $usuario->parqueo_asignado_id,
            'parqueo_nombre' => $usuario->parqueoAsignado->nombre,
        ]);
 
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect()->route('login');
    }

    /**
     * Devuelve al formulario con el error. Si ya se habia
     * autenticado, cierra la sesion antes: de lo contrario
     * quedaria abierta pese a haberse rechazado el acceso.
     */
    private function rechazar(string $mensaje, bool $cerrarSesion = false)
    {
        if ($cerrarSesion) {
            Auth::logout();
        }
 
        return back()
            ->withErrors(['email' => $mensaje])
            ->onlyInput('email');
    }

}
