<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, \Closure $next)
    {
        // Sprawdzamy czy użytkownik jest zalogowany i czy ma rolę admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Jeśli nie jest adminem, przekieruj go na stronę główną
        return redirect('/')->with('error', 'Brak dostępu do panelu administratora.');
    }
}
