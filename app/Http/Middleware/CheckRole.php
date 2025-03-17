<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // Check if user has any of the specified roles
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->role === $role) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
