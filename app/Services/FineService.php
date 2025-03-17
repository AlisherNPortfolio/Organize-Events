<?php

namespace App\Services;

use App\Repositories\Interfaces\UserFineRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Strategies\FineCalculation\FineCalculationStrategyInterface;
use App\Strategies\FineCalculation\DefaultFineStrategy;

class FineService
{    protected $fineStrategy;

    public function __construct(
        protected IUserFineRepository $userFineRepository,
        protected IUserRepository $userRepository,
        IFineCalculationStrategy $fineStrategy = null
    ) {
        $this->fineStrategy = $fineStrategy ?? new DefaultFineStrategy();
    }

    public function applyFine($userId, $eventId, $reason)
    {
        $durationDays = $this->fineStrategy->calculateFineDuration($userId);

        $fine = $this->userFineRepository->create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'reason' => $reason,
            'duration_days' => $durationDays,
            'fine_until' => now()->addDays($durationDays)
        ]);

        $this->userRepository->update([
            'fine_until' => $fine->fine_until
        ], $userId);

        return $fine;
    }

    public function getUserActiveFines($userId)
    {
        return $this->userFineRepository->getUserActiveFines($userId);
    }

    public function setFineStrategy(IFineCalculationStrategy $strategy)
    {
        $this->fineStrategy = $strategy;
        return $this;
    }

    public function checkUserFineStatus($userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user->isFined()) {
            return [
                'is_fined' => false,
                'message' => 'User is not currently fined'
            ];
        }

        $fines = $this->getUserActiveFines($userId);
        $latestFine = $fines->first();

        return [
            'is_fined' => true,
            'until' => $user->fine_until->format('Y-m-d H:i:s'),
            'days_remaining' => $user->fine_until->diffInDays(now()),
            'latest_fine' => $latestFine ? [
                'event_id' => $latestFine->event_id,
                'reason' => $latestFine->reason,
                'duration_days' => $latestFine->duration_days
            ] : null
        ];
    }
}
