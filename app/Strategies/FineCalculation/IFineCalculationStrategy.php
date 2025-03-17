<?php

namespace App\Strategies\FineCalculation;

interface IFineCalculationStrategy
{
    public function calculateFineDuration($userId): int;
}
