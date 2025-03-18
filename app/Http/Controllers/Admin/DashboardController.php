<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use App\Services\EventService;
use App\Services\UserService;

class DashboardController extends Controller
{
    public function __construct(protected UserService $userService, protected EventService $eventService, protected CommonRepository $commonRepository)
    {
    }
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
            'total_users' => $this->commonRepository->getUsersCount(),
            'total_creators' => $this->commonRepository->getCreatorsCount(),
            'total_events' => $this->commonRepository->getEventsCount(),
            'active_events' => $this->commonRepository->getActiveEventsCount(),
            'upcoming_events' => $this->commonRepository->getUpcomingEventsCount(),
            'completed_events' => $this->commonRepository->getCompletedEventsCount(),
            'cancelled_events' => $this->commonRepository->getCancelledEventsCount(),
            'total_fines' => $this->commonRepository->getFinesCount(),
            'active_fines' => $this->commonRepository->getActiveFinesCount(),
        ];

        $latestUsers = $this->commonRepository->getLatestUsers();
        $latestEvents = $this->commonRepository->getLatestEvents();

        return view('admin.dashboard', compact('stats', 'latestUsers', 'latestEvents'));
    }
}
