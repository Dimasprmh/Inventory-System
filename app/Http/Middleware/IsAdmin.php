<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request);
        }

        // Kembalikan halaman 403 (Blade akan render view errors/403.blade.php jika tersedia)
        abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
    }
}
