<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\UserFine;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public static function middleware()
    {
        return [
            'role:admin'
        ];
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_creators' => User::where('role', User::ROLE_CREATOR)->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
            'upcoming_events' => Event::where('status', 'active')->where('event_date', '>=', now())->count(),
            'completed_events' => Event::where('status', 'completed')->count(),
            'cancelled_events' => Event::where('status', 'cancelled')->count(),
            'total_fines' => UserFine::count(),
            'active_fines' => UserFine::where('fine_until', '>', now())->count(),
        ];

        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $latestEvents = Event::with('creator')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestEvents'));
    }
}
