<?php

namespace App\DTO;

class WageCalculationDTO
{
    public function __construct(
        public readonly float $baseWage,
        public readonly float $days,
        public readonly float $manualProcent,
        public readonly array $wageProcentRules, // wage->procent
        public readonly array $formValues,        // данные формы
    ) {}
}
