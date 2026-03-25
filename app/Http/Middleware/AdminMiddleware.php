<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'hr')) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Maaf Anda tidak memiliki hak akses ke halaman ini');
    }
}
