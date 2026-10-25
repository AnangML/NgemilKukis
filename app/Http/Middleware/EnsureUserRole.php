<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request to check against specific allowed roles.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('admin.login'));
        }

        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses yang memadai untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
