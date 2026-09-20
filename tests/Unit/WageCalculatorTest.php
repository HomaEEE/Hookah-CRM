<?php

namespace Tests\Unit;

use Modules\Finance\DTO\WageCalculatorDTO;
use Modules\Finance\Services\WageCalculator;
use PHPUnit\Framework\TestCase;

class WageCalculatorTest extends TestCase
{
    public function test_calculate_with_manual_procent(): void
    {
        $calculator = new WageCalculator;

        $dto = new WageCalculatorDTO(
            baseWage: 500.0,
            days: 1.0,
            manualProcent: 150.0,
            wageProcentRules: [],
            formValues: [],
        );

        $result = $calculator->calculate($dto);

        $this->assertSame(150.0, $result['procent_day']);
        $this->assertSame(650.0, $result['cashout']);
    }
}
