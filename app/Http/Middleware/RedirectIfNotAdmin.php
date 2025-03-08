<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\RolesEnum;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminRoles = [
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value,
            RolesEnum::MANAGER->value
        ];

        if (!$request->user() || !$request->user()->hasAnyRole($adminRoles)) {
            return redirect()->route('index')->with('error', 'Unauthorized access. Admin only area.');
        }

        return $next($request);
    }
}
