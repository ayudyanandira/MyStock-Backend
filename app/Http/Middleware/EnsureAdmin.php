<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role?->name !== Role::ADMIN) {
            abort(403, 'Hanya Admin yang dapat mengelola user.');
        }

        return $next($request);
    }
}
