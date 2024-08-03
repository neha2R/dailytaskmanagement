<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WarehouseHeadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->role->name === 'Warehouse Head') {
            return $next($request);
        }
        return redirect()->back()->with('error', 'You do not have the necessary permissions to access this page.');
    }
}
