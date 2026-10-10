<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class IsPelapor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === UserRole::PELAPOR) {
            return $next($request);
        }

        return redirect('/login')->with('error', 'Akses ditolak. Anda bukan Pelapor.');
    }
}
