<?php

namespace Modules\Finance\DTO;

class WageCalculatorDTO
{
    public function __construct(
        public readonly float $baseWage,
        public readonly float $days,
        public readonly float $manualProcent,
        public readonly array $wageProcentRules,
        public readonly array $formValues,
    ) {}
}
