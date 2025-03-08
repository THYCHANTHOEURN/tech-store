<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\RolesEnum;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole(RolesEnum::CUSTOMER->value)) {
            return redirect()->route('login')->with('error', 'Please login as customer to continue.');
        }

        return $next($request);
    }
}
