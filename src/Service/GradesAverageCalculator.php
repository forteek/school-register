<?php

namespace App\Service;

use App\Enum\GradeValue;
use Countable;

class GradesAverageCalculator
{
    public function calculate(iterable|Countable $grades): float
    {
        $gradesCount = count($grades);
        if ($gradesCount === 0) {
            return 0;
        }

        $sum = 0;
        foreach ($grades as $grade) {
            $sum += GradeValue::VALUE[$grade->getValue()];
        }

        return $sum / $gradesCount;
    }
}
