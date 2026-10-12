<?php

declare(strict_types=1);

namespace App\Modules\Weather\Domain\Contracts;

use App\Modules\Weather\Domain\ValueObjects\Temperature;

interface WeatherProviderInterface
{
    /**
     * Null when the weather service is unavailable or returns no value for the current hour.
     */
    public function currentTemperatureInKatowice(): ?Temperature;
}
