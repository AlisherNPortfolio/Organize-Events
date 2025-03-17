<?php

namespace App\Strategies\FineCalculation;

use App\Repositories\Contracts\IUserFineRepository;

class ProgressiveFineStrategy implements IFineCalculationStrategy
{
    protected $baseDuration = 7;
    protected $maxDuration = 30;

    public function __construct(protected IUserFineRepository $userFineRepository)
    {
        $this->userFineRepository = $userFineRepository;
    }

    public function calculateFineDuration($userId): int
    {
        $previousFinesCount = 0;

        if ($this->userFineRepository) {
            $previousFines = $this->userFineRepository->findBy([
                'user_id' => $userId,
                ['created_at', '>=', now()->subDays(90)]
            ]);

            $previousFinesCount = count($previousFines);
        }

        $duration = $this->baseDuration * ($previousFinesCount + 1);

        return min($duration, $this->maxDuration);
    }
}
