<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventReadOnlyMutation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isReadOnly = session('is_read_only', false)
            || session('user_role') === 'manager'
            || (auth()->check() && auth()->user()->role === 'manager');

        if ($isReadOnly) {
            // Whitelist routes yang boleh diakses walaupun POST (seperti logout)
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;

            if (in_array($routeName, ['logout', 'login', 'login.post'])) {
                return $next($request);
            }

            // Cegah modifikasi data (POST, PUT, PATCH, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak: Akun HSE Manager hanya memiliki hak akses Read-Only (Melihat ringkasan dan data).'
                    ], 403);
                }

                return redirect()->back()->with('error', 'Akses ditolak: Akun HSE Manager bersifat Read-Only (hanya dapat melihat data dan ringkasan).');
            }

            // Cegah pembukaan form input APD langsung via URL
            if ($routeName === 'apd.create') {
                return redirect()->route('apd.index')->with('warning', 'Akun HSE Manager bersifat Read-Only (hanya dapat melihat ringkasan dan data).');
            }
        }

        return $next($request);
    }
}
