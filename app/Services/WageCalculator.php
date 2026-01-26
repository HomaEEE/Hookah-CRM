<?php

namespace App\Services;

use App\DTO\WageCalculationDTO;
use App\Support\WorkshopFieldCache;

class WageCalculator
{
    public function calculate(WageCalculationDTO $dto): array
    {
        $procent = $dto->manualProcent > 0
            ? $dto->manualProcent
            : $this->calculateAutoProcent($dto);

        $procent *= $this->dayMultiplier($dto->days);

        $cashout = ($dto->baseWage * $dto->days) + $procent;

        return [
            'procent_day' => round($procent, 2),
            'cashout' => round($cashout, 2),
        ];
    }

    private function calculateAutoProcent(WageCalculationDTO $dto): float
    {
        $sum = 0;

        foreach ($dto->wageProcentRules as $rule) {
            $field = WorkshopFieldCache::field($rule['workshop_id']);

            if (! $field) {
                continue;
            }

            $value = (float) ($dto->formValues[$field] ?? 0);
            $sum += $value * (float) $rule['num'];
        }

        return $this->roundProcent($sum);
    }

    /**
     * 🔥 Нормальное округление без % 1
     */
    private function roundProcent(float $value): float
    {
        return round($value / 10) * 10;
    }

    /**
     * Универсальная логика смен
     */
    private function dayMultiplier(float $days): float
    {
        return match ($days) {
            0.5 => 1,
            default => 1.0,
        };
    }
}
