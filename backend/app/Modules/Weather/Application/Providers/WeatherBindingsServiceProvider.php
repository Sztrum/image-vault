<?php

declare(strict_types=1);

namespace App\Modules\Weather\Application\Providers;

use App\Core\Application\Providers\ServiceProvider;
use App\Modules\Weather\Domain\Contracts\WeatherProviderInterface;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoResponseMapper;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;
use Psr\Log\LoggerInterface;

class WeatherBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: WeatherProviderInterface::class,
            concrete: function (Application $app): OpenMeteoWeatherProvider {
                $config = $app->make(ConfigContract::class);

                return new OpenMeteoWeatherProvider(
                    http: $app->make(HttpFactory::class),
                    mapper: $app->make(OpenMeteoResponseMapper::class),
                    logger: $app->make(LoggerInterface::class),
                    url: $config->string('weather::weather.open_meteo.url'),
                    timeoutSeconds: $config->integer('weather::weather.open_meteo.timeout_seconds'),
                    timezone: $config->string('weather::weather.open_meteo.timezone'),
                );
            }
        );
    }
}
