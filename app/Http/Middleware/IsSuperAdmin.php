<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === UserRole::SUPERADMIN) {
            return $next($request);
        }

        return redirect('/login')->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
    }
}
