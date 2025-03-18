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
            return $this->getActiveEventsQuery()->get();
    }

    public function getActiveEventsQuery()
    {
        return $this->model->where('status', 'active')
                ->where('event_date', '>=', now()->toDateString())
                ->orderBy('event_date');
    }

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Retrieve all events created by a specific user, ordered by creation date in descending order.
     *
     * @param int $userId The ID of the user whose events are to be retrieved.
     * @return \Illuminate\Database\Eloquent\Collection The collection of events.
     */

/******  8e6571f2-db5a-4194-b805-1bf23d7e9aaf  *******/
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
