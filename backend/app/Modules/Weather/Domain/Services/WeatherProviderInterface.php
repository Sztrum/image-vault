<?php

declare(strict_types=1);

namespace App\Modules\Weather\Domain\Services;

use App\Modules\Weather\Domain\ValueObjects\Temperature;

interface WeatherProviderInterface
{
    public function currentTemperatureInKatowice(): ?Temperature;
}
