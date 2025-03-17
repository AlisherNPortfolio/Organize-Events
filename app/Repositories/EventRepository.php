<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Contracts\IEventRepository;

class EventRepository extends BaseRepository implements IEventRepository
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function findWithParticipants($id)
    {
        return $this->model->with('participants.user')->findOrFail($id);
    }

    public function getActiveEvents()
    {
        return $this->model->where('status', 'active')
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->get();
    }

    public function getUserEvents($userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getEventsWithOpenVoting()
    {
        return $this->model->where('voting_expiry_time', '>', now())
            ->where('status', 'active')
            ->orderBy('voting_expiry_time')
            ->get();
    }

    public function getUpcomingEvents()
    {
        return $this->model->where('event_date', '>=', now()->toDateString())
            ->where('status', 'active')
            ->orderBy('event_date')
            ->take(10)
            ->get();
    }

    public function getEventsByStatus(string $status)
    {
        return $this->model->where('status', $status)
            ->orderBy('event_date')
            ->get();
    }
}
