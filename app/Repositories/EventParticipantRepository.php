<?php

namespace App\Repositories;

use App\Models\EventParticipant;
use App\Repositories\Contracts\IEventParticipantRepository;

class EventParticipantRepository extends BaseRepository implements IEventParticipantRepository
{
    public function __construct(EventParticipant $model)
    {
        parent::__construct($model);
    }

    public function getUserParticipations($userId)
    {
        return $this->model->with('event')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getEventParticipants($eventId)
    {
        return $this->model->with('user')
            ->where('event_id', $eventId)
            ->get();
    }

    public function getParticipant($eventId, $userId)
    {
        return $this->model->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->first();
    }

    public function markAsAttended($id)
    {
        $participant = $this->find($id);
        $participant->markAsAttended();
        return $participant;
    }

    public function markAsNoShow($id)
    {
        $participant = $this->find($id);
        $participant->markAsNoShow();
        return $participant;
    }

    public function deleteParticipation($eventId, $userId)
    {
        return $this->model->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->delete();
    }
}
