<?php

declare(strict_types=1);

namespace App\Modules\Weather\Infrastructure\OpenMeteo;

use App\Modules\Weather\Domain\Contracts\WeatherProviderInterface;
use App\Modules\Weather\Domain\ValueObjects\Temperature;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class OpenMeteoWeatherProvider implements WeatherProviderInterface
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly OpenMeteoResponseMapper $mapper,
        private readonly LoggerInterface $logger,
        private readonly string $url,
        private readonly int $timeoutSeconds,
        private readonly string $timezone,
    ) {}

    public function currentTemperatureInKatowice(): ?Temperature
    {
        $now = CarbonImmutable::now($this->timezone);

        try {
            $payload = $this->http->timeout($this->timeoutSeconds)->get($this->url)->throw()->json();
            $temperature = $this->mapper->toCurrentTemperature($payload, $now);
        } catch (ConnectionException|RequestException|UnexpectedValueException $exception) {
            $this->logger->warning('Open-Meteo temperature unavailable.', ['reason' => $exception->getMessage()]);

            return null;
        }

        if ($temperature === null) {
            $this->logger->warning('Open-Meteo response has no entry for the current hour.', [
                'hour' => $now->format('Y-m-d H:00'),
            ]);
        }

        return $temperature;
    }
}
