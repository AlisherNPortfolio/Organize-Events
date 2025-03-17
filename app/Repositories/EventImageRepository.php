<?php

namespace App\Repositories;

use App\Models\EventImage;
use App\Repositories\Contracts\IEventImageRepository;

class EventImageRepository extends BaseRepository implements IEventImageRepository
{
    public function __construct(EventImage $model)
    {
        parent::__construct($model);
    }

    public function getEventImages($eventId)
    {
        return $this->model->with('user')
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserUploadedImages($userId)
    {
        return $this->model->with('event')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function countEventImages($eventId)
    {
        return $this->model->where('event_id', $eventId)->count();
    }
}
