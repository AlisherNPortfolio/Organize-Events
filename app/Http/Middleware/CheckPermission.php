<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // Check if the user has the required permission
        if (!$user->hasPermission($permission)) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
