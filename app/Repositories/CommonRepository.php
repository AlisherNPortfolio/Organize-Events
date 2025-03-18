<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\User;
use App\Models\UserFine;
use App\Repositories\Contracts\ICommonRepository;

class CommonRepository implements ICommonRepository
{
    public function getUsersCount()
    {
        return User::count();
    }

    public function getEventsCount()
    {
        return Event::count();
    }

    public function getCreatorsCount()
    {
        return User::where('role', User::ROLE_CREATOR)->count();
    }

    public function getActiveEventsCount()
    {
        return Event::where('status', 'active')->count();
    }

    public function getUpcomingEventsCount()
    {
        return Event::where('status', 'active')->where('event_date', '>=', now())->count();
    }

    public function getCompletedEventsCount()
    {
        return Event::where('status', 'completed')->count();
    }

    public function getCancelledEventsCount()
    {
        return Event::where('status', 'cancelled')->count();
    }

    public function getFinesCount()
    {
        return UserFine::count();
    }

    public function getActiveFinesCount()
    {
        return UserFine::where('fine_until', '>', now())->count();
    }
    public function getLatestUsers()
    {
        return User::latest()->take(5)->get();
    }

    public function getLatestEvents()
    {
        return Event::with('creator')->latest()->take(5)->get();
    }

    public function getAllUsers()
    {
        return User::all();
    }
}
