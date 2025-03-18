<?php

namespace App\Http\Middleware;

use App\Services\FineService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserFine
{
    public function __construct(protected FineService $fineService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($this->fineService->isUserFined($user->id)) {
            $fineStatus = $this->fineService->checkUserFineStatus($user->id);

            $allowedRoutes = ['profile', 'profile.edit', 'profile.update', 'logout'];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {

                if (str_contains($request->path(), 'events') &&
                    (str_contains($request->path(), 'vote') || str_contains($request->path(), 'create'))) {
                    return redirect()->route('profile')
                        ->with('error', 'Sizga jarima berilganligi sababli' .
                            $fineStatus['until'] . ' gacha ovoz bera olmaysiz (ovoz berish ochilishiga ' . $fineStatus['days_remaining'] . ' kun qoldi).');
                }
            }
        }

        return $next($request);
    }
}
