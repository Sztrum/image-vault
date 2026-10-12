<?php

declare(strict_types=1);

namespace App\Modules\Weather\Infrastructure\OpenMeteo;

use App\Modules\Weather\Domain\ValueObjects\Temperature;
use Carbon\CarbonImmutable;
use UnexpectedValueException;

class OpenMeteoResponseMapper
{
    private const string HOUR_FORMAT = 'Y-m-d\TH:00';

    /**
     * Open-Meteo returns a list of hours, not the current value: picks the entry for the hour of $now.
     *
     * @return Temperature|null null when the response has no entry for the current hour
     *
     * @throws UnexpectedValueException when the response does not have the expected shape
     */
    public function toCurrentTemperature(mixed $payload, CarbonImmutable $now): ?Temperature
    {
        $times = data_get($payload, 'hourly.time');
        $temperatures = data_get($payload, 'hourly.temperature_2m');

        throw_if(
            !is_array($times) || !is_array($temperatures),
            UnexpectedValueException::class,
            'Open-Meteo response has no hourly.time or hourly.temperature_2m list.'
        );

        $index = array_search($now->format(self::HOUR_FORMAT), $times, true);
        if ($index === false) {
            return null;
        }

        $celsius = $temperatures[$index] ?? null;
        throw_if(
            !is_int($celsius) && !is_float($celsius),
            UnexpectedValueException::class,
            'Open-Meteo response has no numeric temperature for the current hour.'
        );

        return new Temperature(celsius: (float) $celsius, measuredAt: $now->startOfHour());
    }
}
