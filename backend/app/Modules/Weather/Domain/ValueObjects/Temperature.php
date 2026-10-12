<?php

declare(strict_types=1);

namespace App\Modules\Weather\Domain\ValueObjects;

use Carbon\CarbonImmutable;

final readonly class Temperature
{
    public function __construct(
        public float $celsius,
        public CarbonImmutable $measuredAt,
    ) {}
}
