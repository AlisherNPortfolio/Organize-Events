<?php

namespace App\Factories;

use App\Repositories\Contracts\IUserFineRepository;
use App\Strategies\FineCalculation\DefaultFineStrategy;
use App\Strategies\FineCalculation\IFineCalculationStrategy;
use App\Strategies\FineCalculation\ProgressiveFineStrategy;

class FineCalculationStrategyFactory
{

    public function __construct(public IUserFineRepository $userFineRepository)
    {
        $this->userFineRepository = $userFineRepository;
    }

    public function createStrategy(string $type): IFineCalculationStrategy
    {
        switch ($type) {
            case 'default':
                return new DefaultFineStrategy($this->userFineRepository);
            case 'progressive':
                return new ProgressiveFineStrategy($this->userFineRepository);
            default:
                throw new \InvalidArgumentException("Noto\'g\'ri jarimani hisoblash strategy turi: {$type}");
        }
    }
}
