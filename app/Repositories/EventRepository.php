<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Contracts\IEventRepository;
use Illuminate\Support\Facades\DB;

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
            return $this->getActiveEventsQuery()->get();
    }

    public function getActiveEventsQuery()
    {
        return $this->model->where('status', 'active')
                ->where('event_date', '>=', now()->toDateString())
                ->orderBy('event_date');
    }

    public function getUserEvents($userId, array $relations = [])
    {
        $query = $this->model;
        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->where('events.user_id', $userId)
            ->orderBy('events.created_at', 'desc')
            ->paginate();
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
        return $this->getUpcomingEventsQuery()
            ->take(10)
            ->get();
    }

    public function getUpcomingEventsQuery()
    {
        return $this->model->where('event_date', '>=', now()->toDateString())
            ->where('status', 'active')
            ->orderBy('event_date');
    }

    public function getEventsByStatus(string $status)
    {
        return $this->model->where('status', $status)
            ->orderBy('event_date')
            ->get();
    }
}
