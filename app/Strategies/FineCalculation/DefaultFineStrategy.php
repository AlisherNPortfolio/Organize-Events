<?php

namespace App\Strategies\FineCalculation;

use App\Repositories\Contracts\IUserFineRepository;

class DefaultFineStrategy implements IFineCalculationStrategy
{
    protected $defaultDuration = 7;

    public function __construct(protected IUserFineRepository $userFineRepository)
    {
    }

    public function calculateFineDuration($userId): int
    {
        return $this->defaultDuration;
    }
}
