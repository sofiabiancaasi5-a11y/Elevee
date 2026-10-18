<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Please sign in to access the administrator portal.');
        }

        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
