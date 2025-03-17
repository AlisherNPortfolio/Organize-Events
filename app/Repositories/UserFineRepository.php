<?php

namespace App\Repositories;

use App\Models\UserFine;
use App\Repositories\Contracts\IUserFineRepository;
use Carbon\Carbon;

class UserFineRepository extends BaseRepository implements IUserFineRepository
{
    public function __construct(UserFine $model)
    {
        parent::__construct($model);
    }

    public function getUserActiveFines($userId)
    {
        return $this->model->with('event')
            ->where('user_id', $userId)
            ->where('fine_until', '>', now())
            ->orderBy('fine_until', 'desc')
            ->get();
    }

    public function createFineForNoShow($userId, $eventId)
    {
        $fineUntil = Carbon::now()->addDays(7);

        return $this->create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'reason' => 'No-show to event',
            'duration_days' => 7,
            'fine_until' => $fineUntil,
        ]);
    }

    public function checkActiveFineDuration($userId)
    {
        $latestFine = $this->model->where('user_id', $userId)
            ->where('fine_until', '>', now())
            ->orderBy('fine_until', 'desc')
            ->first();

        return $latestFine ? $latestFine->fine_until : null;
    }
}
